package com.meditrack.kurir.data.api

import com.meditrack.kurir.data.model.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {

    @POST("kurir/login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @POST("kurir/logout")
    suspend fun logout(): Response<Unit>

    @GET("kurir/orders")
    suspend fun getOrders(): Response<OrdersResponse>

    @GET("kurir/orders/history")
    suspend fun getOrderHistory(): Response<OrdersResponse>

    @PATCH("kurir/orders/{id}/status")
    suspend fun updateStatus(
        @Path("id") orderId: Int,
        @Body request: StatusRequest
    ): Response<StatusResponse>

    @POST("kurir/location")
    suspend fun updateLocation(@Body request: LocationRequest): Response<Unit>

    @POST("kurir/orders/pickup")
    suspend fun pickupOrders(@Body request: BatchPickupRequest): Response<BatchPickupResponse>
}
