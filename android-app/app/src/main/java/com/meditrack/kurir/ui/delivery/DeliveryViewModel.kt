package com.meditrack.kurir.ui.delivery

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.meditrack.kurir.data.repository.OrderRepository
import com.meditrack.kurir.data.repository.Result
import kotlinx.coroutines.launch

class DeliveryViewModel(private val repository: OrderRepository) : ViewModel() {

    private val _deliveryStarted = MutableLiveData(false)
    val deliveryStarted: LiveData<Boolean> = _deliveryStarted

    private val _deliveryCompleted = MutableLiveData(false)
    val deliveryCompleted: LiveData<Boolean> = _deliveryCompleted

    private val _isLoading = MutableLiveData(false)
    val isLoading: LiveData<Boolean> = _isLoading

    private val _error = MutableLiveData<String?>()
    val error: LiveData<String?> = _error

    fun startDelivery(orderId: Int) {
        _isLoading.value = true
        viewModelScope.launch {
            when (val result = repository.startDelivery(orderId)) {
                is Result.Success -> {
                    _isLoading.value = false
                    _deliveryStarted.value = true
                }
                is Result.Error -> {
                    _isLoading.value = false
                    _error.value = result.message
                }
            }
        }
    }

    fun completeDelivery(orderId: Int) {
        _isLoading.value = true
        viewModelScope.launch {
            when (val result = repository.completeDelivery(orderId)) {
                is Result.Success -> {
                    _isLoading.value = false
                    _deliveryCompleted.value = true
                }
                is Result.Error -> {
                    _isLoading.value = false
                    _error.value = result.message
                }
            }
        }
    }
}
