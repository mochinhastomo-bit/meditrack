package com.meditrack.kurir.service

import android.app.*
import android.content.Intent
import android.os.*
import android.util.Log
import androidx.core.app.NotificationCompat
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import com.google.android.gms.location.*
import com.meditrack.kurir.R
import com.meditrack.kurir.data.api.RetrofitClient
import com.meditrack.kurir.data.model.LocationRequest
import com.meditrack.kurir.utils.Constants
import com.meditrack.kurir.utils.SharedPrefManager
import kotlinx.coroutines.*

class LocationService : Service() {

    private val binder = LocalBinder()
    private lateinit var fusedLocationClient: FusedLocationProviderClient
    private lateinit var locationCallback: LocationCallback

    private val _currentLocation = MutableLiveData<android.location.Location?>()
    val currentLocation: LiveData<android.location.Location?> = _currentLocation

    private val serviceScope = CoroutineScope(Dispatchers.IO + SupervisorJob())
    private var lastUploadTime = 0L
    private var orderId = -1

    inner class LocalBinder : Binder() {
        fun getService(): LocationService = this@LocationService
    }

    override fun onBind(intent: Intent?): IBinder = binder

    override fun onCreate() {
        super.onCreate()
        fusedLocationClient = LocationServices.getFusedLocationProviderClient(this)
        createNotificationChannel()
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        orderId = intent?.getIntExtra(Constants.EXTRA_ORDER_ID, -1) ?: -1
        startForeground(Constants.NOTIF_ID, buildNotification())
        startLocationUpdates()
        return START_STICKY
    }

    private fun startLocationUpdates() {
        val request = com.google.android.gms.location.LocationRequest.Builder(
            Priority.PRIORITY_HIGH_ACCURACY,
            Constants.GPS_INTERVAL_MS
        )
            .setMinUpdateIntervalMillis(Constants.GPS_FASTEST_INTERVAL_MS)
            .build()

        locationCallback = object : LocationCallback() {
            override fun onLocationResult(result: LocationResult) {
                result.lastLocation?.let { location ->
                    _currentLocation.postValue(location)
                    maybeUploadLocation(location)
                }
            }
        }

        try {
            fusedLocationClient.requestLocationUpdates(
                request,
                locationCallback,
                Looper.getMainLooper()
            )
        } catch (e: SecurityException) {
            Log.e("LocationService", "Izin GPS tidak diberikan: ${e.message}")
        }
    }

    private fun maybeUploadLocation(location: android.location.Location) {
        val now = System.currentTimeMillis()
        if (now - lastUploadTime < Constants.LOCATION_UPDATE_INTERVAL) return
        lastUploadTime = now

        serviceScope.launch {
            try {
                val prefs = SharedPrefManager.getInstance(applicationContext)
                val api   = RetrofitClient.getInstance(prefs)
                api.updateLocation(LocationRequest(location.latitude, location.longitude))
                Log.d("LocationService", "Lokasi dikirim: ${location.latitude}, ${location.longitude}")
            } catch (e: Exception) {
                Log.e("LocationService", "Gagal kirim lokasi: ${e.message}")
            }
        }
    }

    private fun createNotificationChannel() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                Constants.NOTIF_CHANNEL_ID,
                Constants.NOTIF_CHANNEL_NAME,
                NotificationManager.IMPORTANCE_LOW
            ).apply {
                description = "GPS aktif selama pengiriman"
                setShowBadge(false)
            }
            val manager = getSystemService(NotificationManager::class.java)
            manager.createNotificationChannel(channel)
        }
    }

    private fun buildNotification(): Notification {
        return NotificationCompat.Builder(this, Constants.NOTIF_CHANNEL_ID)
            .setContentTitle("MediTrack — GPS Aktif")
            .setContentText("Lokasi Anda sedang dilacak untuk pengiriman obat")
            .setSmallIcon(R.drawable.ic_local_shipping)
            .setOngoing(true)
            .setPriority(NotificationCompat.PRIORITY_LOW)
            .build()
    }

    override fun onDestroy() {
        super.onDestroy()
        fusedLocationClient.removeLocationUpdates(locationCallback)
        serviceScope.cancel()
    }
}
