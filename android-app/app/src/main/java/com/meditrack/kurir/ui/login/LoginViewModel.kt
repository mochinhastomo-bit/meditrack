package com.meditrack.kurir.ui.login

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.meditrack.kurir.data.model.LoginResponse
import com.meditrack.kurir.data.repository.AuthRepository
import com.meditrack.kurir.data.repository.Result
import kotlinx.coroutines.launch

class LoginViewModel(private val repository: AuthRepository) : ViewModel() {

    private val _loginState = MutableLiveData<LoginState>()
    val loginState: LiveData<LoginState> = _loginState

    fun login(email: String, password: String) {
        if (email.isBlank() || password.isBlank()) {
            _loginState.value = LoginState.Error("Email dan password tidak boleh kosong")
            return
        }

        _loginState.value = LoginState.Loading
        viewModelScope.launch {
            when (val result = repository.login(email, password)) {
                is Result.Success -> _loginState.value = LoginState.Success(result.data)
                is Result.Error   -> _loginState.value = LoginState.Error(result.message)
            }
        }
    }
}

sealed class LoginState {
    object Loading : LoginState()
    data class Success(val data: LoginResponse) : LoginState()
    data class Error(val message: String) : LoginState()
}
