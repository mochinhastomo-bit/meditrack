package com.meditrack.kurir.data.repository

import com.meditrack.kurir.data.api.ApiService
import com.meditrack.kurir.data.model.LoginRequest
import com.meditrack.kurir.data.model.LoginResponse
import com.meditrack.kurir.utils.SharedPrefManager

sealed class Result<out T> {
    data class Success<T>(val data: T) : Result<T>()
    data class Error(val message: String) : Result<Nothing>()
}

class AuthRepository(
    private val api: ApiService,
    private val prefs: SharedPrefManager
) {
    suspend fun login(email: String, password: String): Result<LoginResponse> {
        return try {
            val response = api.login(LoginRequest(email, password))
            if (response.isSuccessful && response.body() != null) {
                val body = response.body()!!
                prefs.token      = body.token
                prefs.userName   = body.user.name
                prefs.userEmail  = body.user.email
                prefs.courierId  = body.courier?.id ?: -1
                Result.Success(body)
            } else {
                val msg = response.errorBody()?.string() ?: "Login gagal"
                Result.Error(msg)
            }
        } catch (e: Exception) {
            Result.Error("Tidak dapat terhubung ke server: ${e.localizedMessage}")
        }
    }

    suspend fun logout(): Result<Unit> {
        return try {
            api.logout()
            prefs.clear()
            Result.Success(Unit)
        } catch (e: Exception) {
            prefs.clear()
            Result.Success(Unit)
        }
    }
}
