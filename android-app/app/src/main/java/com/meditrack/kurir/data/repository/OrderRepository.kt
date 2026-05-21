package com.meditrack.kurir.data.repository

import com.meditrack.kurir.data.api.ApiService
import com.meditrack.kurir.data.model.*

class OrderRepository(private val api: ApiService) {

    suspend fun getOrders(): Result<List<Order>> {
        return try {
            val response = api.getOrders()
            if (response.isSuccessful && response.body() != null) {
                Result.Success(response.body()!!.data)
            } else {
                Result.Error("Gagal memuat data: ${response.code()}")
            }
        } catch (e: Exception) {
            Result.Error("Tidak dapat terhubung ke server")
        }
    }

    suspend fun pickupOrders(orderIds: List<Int>): Result<BatchPickupResponse> {
        return try {
            val response = api.pickupOrders(BatchPickupRequest(orderIds))
            if (response.isSuccessful && response.body() != null) {
                Result.Success(response.body()!!)
            } else {
                Result.Error("Gagal mengambil resep: ${response.code()}")
            }
        } catch (e: Exception) {
            Result.Error("Tidak dapat terhubung ke server")
        }
    }

    suspend fun startDelivery(orderId: Int): Result<StatusResponse> {
        return try {
            val response = api.updateStatus(orderId, StatusRequest("dalam_pengiriman"))
            if (response.isSuccessful && response.body() != null) {
                Result.Success(response.body()!!)
            } else {
                val errorMsg = response.errorBody()?.string()
                    ?.let { runCatching { org.json.JSONObject(it).getString("message") }.getOrNull() }
                    ?: "Gagal memperbarui status"
                Result.Error(errorMsg)
            }
        } catch (e: Exception) {
            Result.Error("Tidak dapat terhubung ke server")
        }
    }

    suspend fun completeDelivery(orderId: Int): Result<StatusResponse> {
        return try {
            val response = api.updateStatus(orderId, StatusRequest("terkirim"))
            if (response.isSuccessful && response.body() != null) {
                Result.Success(response.body()!!)
            } else {
                Result.Error("Gagal memperbarui status: ${response.code()}")
            }
        } catch (e: Exception) {
            Result.Error("Tidak dapat terhubung ke server")
        }
    }

    suspend fun updateLocation(lat: Double, lng: Double): Result<Unit> {
        return try {
            val response = api.updateLocation(LocationRequest(lat, lng))
            if (response.isSuccessful) Result.Success(Unit)
            else Result.Error("Gagal kirim lokasi")
        } catch (e: Exception) {
            Result.Error("Gagal kirim lokasi")
        }
    }
}
