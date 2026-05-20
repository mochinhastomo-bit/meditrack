package com.meditrack.kurir.utils

object Constants {
    /**
     * Ganti dengan IP server Laravel saat testing di HP fisik.
     * Emulator Android: gunakan 10.0.2.2 (alias localhost)
     * HP fisik di jaringan lokal: gunakan IP komputer, misal http://192.168.1.5:8000/api/
     */
    const val BASE_URL = "http://10.0.2.2:8000/api/"

    // SharedPreferences
    const val PREF_NAME        = "meditrack_prefs"
    const val PREF_TOKEN       = "auth_token"
    const val PREF_USER_NAME   = "user_name"
    const val PREF_USER_EMAIL  = "user_email"
    const val PREF_COURIER_ID  = "courier_id"

    // Location Service
    const val LOCATION_UPDATE_INTERVAL = 15_000L   // 15 detik kirim ke server
    const val GPS_INTERVAL_MS          = 5_000L    // 5 detik ambil GPS
    const val GPS_FASTEST_INTERVAL_MS  = 3_000L

    // Jarak (meter) untuk aktifkan tombol Selesai
    const val ARRIVAL_RADIUS_METERS = 100f

    // Notification
    const val NOTIF_CHANNEL_ID  = "meditrack_location"
    const val NOTIF_CHANNEL_NAME = "GPS Pengiriman"
    const val NOTIF_ID          = 1001

    // Intent extras
    const val EXTRA_ORDER_ID     = "extra_order_id"
    const val EXTRA_ORDER_NOMOR  = "extra_order_nomor"
    const val EXTRA_DEST_LAT     = "extra_dest_lat"
    const val EXTRA_DEST_LNG     = "extra_dest_lng"
    const val EXTRA_DEST_LABEL   = "extra_dest_label"
    const val EXTRA_DEST_ADDRESS = "extra_dest_address"
    const val EXTRA_PATIENT_NAME = "extra_patient_name"
}
