package com.meditrack.kurir.ui.delivery

import android.Manifest
import android.content.ComponentName
import android.content.Context
import android.content.Intent
import android.content.ServiceConnection
import android.content.pm.PackageManager
import android.graphics.Color
import android.location.Location
import android.os.Bundle
import android.os.IBinder
import android.view.View
import android.widget.Toast
import androidx.activity.result.contract.ActivityResultContracts
import androidx.activity.viewModels
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.google.android.gms.maps.CameraUpdateFactory
import com.google.android.gms.maps.GoogleMap
import com.google.android.gms.maps.OnMapReadyCallback
import com.google.android.gms.maps.SupportMapFragment
import com.google.android.gms.maps.model.*
import com.meditrack.kurir.R
import com.meditrack.kurir.data.api.RetrofitClient
import com.meditrack.kurir.data.repository.OrderRepository
import com.meditrack.kurir.databinding.ActivityDeliveryBinding
import com.meditrack.kurir.service.LocationService
import com.meditrack.kurir.utils.Constants
import com.meditrack.kurir.utils.SharedPrefManager
import kotlin.math.roundToInt

class DeliveryActivity : AppCompatActivity(), OnMapReadyCallback {

    private lateinit var binding: ActivityDeliveryBinding
    private lateinit var prefs: SharedPrefManager
    private var googleMap: GoogleMap? = null

    private var orderId: Int       = -1
    private var orderNomor: String = ""
    private var orderStatus: String = ""
    private var patientName: String = ""
    private var destLat: Double    = 0.0
    private var destLng: Double    = 0.0
    private var destLabel: String  = ""
    private var destAddress: String = ""

    // Marker & polyline
    private var courierMarker: Marker? = null
    private var destMarker: Marker? = null
    private var routeLine: Polyline? = null

    // Service binding
    private var locationService: LocationService? = null
    private var isServiceBound = false

    private val serviceConnection = object : ServiceConnection {
        override fun onServiceConnected(name: ComponentName?, binder: IBinder?) {
            val localBinder = binder as LocationService.LocalBinder
            locationService = localBinder.getService()
            isServiceBound = true

            // Observe location dari service
            locationService?.currentLocation?.observe(this@DeliveryActivity) { location ->
                location?.let { onLocationUpdate(it) }
            }
        }
        override fun onServiceDisconnected(name: ComponentName?) {
            locationService = null
            isServiceBound = false
        }
    }

    private val viewModel: DeliveryViewModel by viewModels {
        object : ViewModelProvider.Factory {
            override fun <T : ViewModel> create(modelClass: Class<T>): T {
                val api  = RetrofitClient.getInstance(prefs)
                val repo = OrderRepository(api)
                @Suppress("UNCHECKED_CAST")
                return DeliveryViewModel(repo) as T
            }
        }
    }

    // Permission launcher
    private val permissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestMultiplePermissions()
    ) { permissions ->
        if (permissions.all { it.value }) {
            startLocationService()
        } else {
            Toast.makeText(this, "Izin GPS diperlukan untuk pengiriman", Toast.LENGTH_LONG).show()
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityDeliveryBinding.inflate(layoutInflater)
        setContentView(binding.root)

        prefs = SharedPrefManager.getInstance(this)

        // Baca data order dari intent
        orderId     = intent.getIntExtra(Constants.EXTRA_ORDER_ID, -1)
        orderNomor  = intent.getStringExtra(Constants.EXTRA_ORDER_NOMOR) ?: ""
        orderStatus = intent.getStringExtra(Constants.EXTRA_ORDER_STATUS) ?: ""
        patientName = intent.getStringExtra(Constants.EXTRA_PATIENT_NAME) ?: ""
        destLat     = intent.getDoubleExtra(Constants.EXTRA_DEST_LAT, 0.0)
        destLng     = intent.getDoubleExtra(Constants.EXTRA_DEST_LNG, 0.0)
        destLabel   = intent.getStringExtra(Constants.EXTRA_DEST_LABEL) ?: ""
        destAddress = intent.getStringExtra(Constants.EXTRA_DEST_ADDRESS) ?: ""

        setupUI()
        setupMap()
        setupObservers()
    }

    private fun setupUI() {
        binding.tvNomorResep.text  = orderNomor
        binding.tvPatientName.text = patientName
        binding.tvDestLabel.text   = destLabel
        binding.tvDestAddress.text = destAddress

        binding.btnBack.setOnClickListener { onBackPressedDispatcher.onBackPressed() }

        // Jika sudah dalam_pengiriman, langsung tampilkan mode kirim
        if (orderStatus == "dalam_pengiriman") {
            binding.btnStartDelivery.visibility  = View.GONE
            binding.layoutDelivering.visibility  = View.VISIBLE
            binding.tvStatusInfo.text = "🚀 Sedang dalam pengiriman..."
            binding.tvStatusInfo.setTextColor(Color.parseColor("#7627BB"))
            requestLocationPermissions()
        } else {
            binding.btnStartDelivery.text = "Mulai Antar"
        }

        binding.btnStartDelivery.setOnClickListener {
            AlertDialog.Builder(this)
                .setTitle("Mulai Pengiriman")
                .setMessage("Apakah Anda siap memulai pengiriman untuk $orderNomor?")
                .setPositiveButton("Ya, Mulai") { _, _ -> startDelivery() }
                .setNegativeButton("Batal", null)
                .show()
        }

        binding.btnComplete.setOnClickListener {
            AlertDialog.Builder(this)
                .setTitle("Konfirmasi Selesai")
                .setMessage("Konfirmasi bahwa obat telah diterima oleh pasien $patientName?")
                .setPositiveButton("Selesai") { _, _ -> completeDelivery() }
                .setNegativeButton("Batal", null)
                .show()
        }

        // Tombol Selesai awalnya non-aktif
        binding.btnComplete.isEnabled = false
        binding.btnComplete.alpha = 0.4f
    }

    private fun setupMap() {
        val mapFragment = supportFragmentManager
            .findFragmentById(R.id.mapFragment) as SupportMapFragment
        mapFragment.getMapAsync(this)
    }

    override fun onMapReady(map: GoogleMap) {
        googleMap = map
        map.uiSettings.isZoomControlsEnabled = true
        map.uiSettings.isMyLocationButtonEnabled = false

        // Pasang marker tujuan
        if (destLat != 0.0 && destLng != 0.0) {
            val destPos = LatLng(destLat, destLng)
            destMarker = map.addMarker(
                MarkerOptions()
                    .position(destPos)
                    .title("Tujuan: $destLabel")
                    .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN))
            )
            map.moveCamera(CameraUpdateFactory.newLatLngZoom(destPos, 15f))
        }
    }

    private fun startDelivery() {
        viewModel.startDelivery(orderId)
    }

    private fun completeDelivery() {
        viewModel.completeDelivery(orderId)
    }

    private fun setupObservers() {
        viewModel.deliveryStarted.observe(this) { started ->
            if (started) {
                // Ubah UI ke mode "sedang kirim"
                binding.btnStartDelivery.visibility = View.GONE
                binding.layoutDelivering.visibility = View.VISIBLE
                binding.tvStatusInfo.text = "🚀 Sedang dalam pengiriman..."
                binding.tvStatusInfo.setTextColor(Color.parseColor("#7627BB"))

                // Minta izin GPS dan start service
                requestLocationPermissions()
            }
        }

        viewModel.deliveryCompleted.observe(this) { completed ->
            if (completed) {
                stopLocationService()
                Toast.makeText(this, "✅ Pengiriman selesai!", Toast.LENGTH_LONG).show()
                finish()
            }
        }

        viewModel.error.observe(this) { msg ->
            msg?.let { Toast.makeText(this, it, Toast.LENGTH_LONG).show() }
        }

        viewModel.isLoading.observe(this) { loading ->
            binding.progressBar.visibility = if (loading) View.VISIBLE else View.GONE
            binding.btnStartDelivery.isEnabled = !loading
        }
    }

    private fun requestLocationPermissions() {
        val permissions = arrayOf(
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION
        )
        if (permissions.all {
            ContextCompat.checkSelfPermission(this, it) == PackageManager.PERMISSION_GRANTED
        }) {
            startLocationService()
        } else {
            permissionLauncher.launch(permissions)
        }
    }

    private fun startLocationService() {
        val intent = Intent(this, LocationService::class.java).apply {
            putExtra(Constants.EXTRA_ORDER_ID, orderId)
        }
        ContextCompat.startForegroundService(this, intent)
        bindService(intent, serviceConnection, Context.BIND_AUTO_CREATE)
    }

    private fun stopLocationService() {
        if (isServiceBound) {
            unbindService(serviceConnection)
            isServiceBound = false
        }
        stopService(Intent(this, LocationService::class.java))
    }

    private fun onLocationUpdate(location: Location) {
        val courierPos = LatLng(location.latitude, location.longitude)

        // Update atau buat marker kurir
        if (courierMarker == null) {
            courierMarker = googleMap?.addMarker(
                MarkerOptions()
                    .position(courierPos)
                    .title("Posisi Anda")
                    .icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_BLUE))
            )
            // Kamera ikut kurir
            googleMap?.animateCamera(CameraUpdateFactory.newLatLngZoom(courierPos, 15f))
        } else {
            courierMarker?.position = courierPos
        }

        // Gambar garis ke tujuan
        if (destLat != 0.0 && destLng != 0.0) {
            routeLine?.remove()
            routeLine = googleMap?.addPolyline(
                PolylineOptions()
                    .add(courierPos, LatLng(destLat, destLng))
                    .color(Color.parseColor("#1A73E8"))
                    .width(6f)
                    .geodesic(true)
            )

            // Hitung jarak ke tujuan
            val result = FloatArray(1)
            Location.distanceBetween(
                location.latitude, location.longitude,
                destLat, destLng,
                result
            )
            val distanceM = result[0]
            val distanceText = if (distanceM >= 1000) {
                "%.1f km".format(distanceM / 1000)
            } else {
                "${distanceM.roundToInt()} m"
            }

            binding.tvDistance.text = "Jarak ke tujuan: $distanceText"

            // Aktifkan tombol Selesai jika sudah ≤ 100 meter
            val canComplete = distanceM <= Constants.ARRIVAL_RADIUS_METERS
            binding.btnComplete.isEnabled = canComplete
            binding.btnComplete.alpha     = if (canComplete) 1f else 0.4f

            if (canComplete) {
                binding.tvDistance.text = "✅ Sudah dekat! Konfirmasi pengiriman"
                binding.tvDistance.setTextColor(Color.parseColor("#137333"))
            } else {
                binding.tvDistance.setTextColor(Color.parseColor("#5F6368"))
            }
        }
    }

    override fun onDestroy() {
        super.onDestroy()
        if (isServiceBound) {
            unbindService(serviceConnection)
        }
    }
}
