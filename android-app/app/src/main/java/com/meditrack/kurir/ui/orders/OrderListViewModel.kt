package com.meditrack.kurir.ui.orders

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.meditrack.kurir.data.model.Order
import com.meditrack.kurir.data.repository.OrderRepository
import com.meditrack.kurir.data.repository.Result
import kotlinx.coroutines.launch

class OrderListViewModel(private val repository: OrderRepository) : ViewModel() {

    private val _orders = MutableLiveData<List<Order>>()
    val orders: LiveData<List<Order>> = _orders

    private val _isLoading = MutableLiveData(false)
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    fun loadOrders() {
        _isLoading.value = true
        _error.value = null
        viewModelScope.launch {
            when (val result = repository.getOrders()) {
                is Result.Success -> {
                    _orders.value = result.data
                    _isLoading.value = false
                }
                is Result.Error -> {
                    _error.value = result.message
                    _isLoading.value = false
                }
            }
        }
    }
}
