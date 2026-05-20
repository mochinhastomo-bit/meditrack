package com.meditrack.kurir.data.model

import com.google.gson.annotations.SerializedName

// ── REQUEST ──────────────────────────────────────────────────────────────

data class LoginRequest(
    val email: String,
    val password: String
)

data class LocationRequest(
    val latitude: Double,
    val longitude: Double
)

data class StatusRequest(
    val status: String
)

// ── RESPONSE ─────────────────────────────────────────────────────────────

data class LoginResponse(
    val token: String,
    val user: UserData,
    val courier: CourierData?
)

data class UserData(
    val id: Int,
    val name: String,
    val email: String,
    val role: String
)

data class CourierData(
    val id: Int,
    val name: String,
    @SerializedName("plate_number") val plateNumber: String,
    val phone: String?,
    @SerializedName("last_latitude")  val lastLatitude: Double?,
    @SerializedName("last_longitude") val lastLongitude: Double?
)

data class OrdersResponse(
    val data: List<Order>
)

data class Order(
    val id: Int,
    @SerializedName("nomor_resep")   val nomorResep: String,
    val tanggal: String,
    val status: String,
    @SerializedName("status_label")  val statusLabel: String,
    @SerializedName("status_color")  val statusColor: String,
    val patient: Patient?,
    val address: Address?,
    val courier: CourierData?,
    val keterangan: String?
)

data class Patient(
    val id: Int,
    val name: String,
    val phone: String?
)

data class Address(
    val id: Int,
    val label: String,
    val address: String,
    val latitude: Double?,
    val longitude: Double?
)

data class StatusResponse(
    val success: Boolean,
    val message: String,
    @SerializedName("new_status") val newStatus: String,
    @SerializedName("status_label") val statusLabel: String
)

data class ApiError(
    val message: String
)
