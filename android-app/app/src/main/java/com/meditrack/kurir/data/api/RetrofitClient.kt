package com.meditrack.kurir.data.api

import com.meditrack.kurir.utils.Constants
import com.meditrack.kurir.utils.SharedPrefManager
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit

object RetrofitClient {

    @Volatile private var instance: ApiService? = null

    fun getInstance(prefManager: SharedPrefManager): ApiService =
        instance ?: synchronized(this) {
            instance ?: buildService(prefManager).also { instance = it }
        }

    private fun buildService(prefManager: SharedPrefManager): ApiService {
        val logging = HttpLoggingInterceptor().apply {
            level = HttpLoggingInterceptor.Level.BODY
        }

        val client = OkHttpClient.Builder()
            .addInterceptor { chain ->
                val token = prefManager.token
                val request = if (!token.isNullOrEmpty()) {
                    chain.request().newBuilder()
                        .addHeader("Authorization", "Bearer $token")
                        .addHeader("Accept", "application/json")
                        .build()
                } else {
                    chain.request().newBuilder()
                        .addHeader("Accept", "application/json")
                        .build()
                }
                chain.proceed(request)
            }
            .addInterceptor(logging)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .build()

        return Retrofit.Builder()
            .baseUrl(Constants.BASE_URL)
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
            .create(ApiService::class.java)
    }

    fun reset() { instance = null }
}
