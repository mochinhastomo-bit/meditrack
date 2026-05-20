package com.meditrack.kurir.ui.login

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.meditrack.kurir.data.api.RetrofitClient
import com.meditrack.kurir.data.repository.AuthRepository
import com.meditrack.kurir.databinding.ActivityLoginBinding
import com.meditrack.kurir.ui.orders.OrderListActivity
import com.meditrack.kurir.utils.SharedPrefManager

class LoginActivity : AppCompatActivity() {

    private lateinit var binding: ActivityLoginBinding
    private lateinit var prefs: SharedPrefManager

    private val viewModel: LoginViewModel by viewModels {
        object : ViewModelProvider.Factory {
            override fun <T : ViewModel> create(modelClass: Class<T>): T {
                val api  = RetrofitClient.getInstance(prefs)
                val repo = AuthRepository(api, prefs)
                @Suppress("UNCHECKED_CAST")
                return LoginViewModel(repo) as T
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityLoginBinding.inflate(layoutInflater)
        setContentView(binding.root)

        prefs = SharedPrefManager.getInstance(this)

        // Sudah login → langsung ke order list
        if (prefs.isLoggedIn) {
            goToOrders()
            return
        }

        setupObservers()
        setupClickListeners()
    }

    private fun setupObservers() {
        viewModel.loginState.observe(this) { state ->
            when (state) {
                is LoginState.Loading -> showLoading(true)
                is LoginState.Success -> {
                    showLoading(false)
                    goToOrders()
                }
                is LoginState.Error -> {
                    showLoading(false)
                    Toast.makeText(this, state.message, Toast.LENGTH_LONG).show()
                }
            }
        }
    }

    private fun setupClickListeners() {
        binding.btnLogin.setOnClickListener {
            val email    = binding.etEmail.text.toString().trim()
            val password = binding.etPassword.text.toString()
            viewModel.login(email, password)
        }
    }

    private fun showLoading(show: Boolean) {
        binding.progressBar.visibility = if (show) View.VISIBLE else View.GONE
        binding.btnLogin.isEnabled     = !show
        binding.etEmail.isEnabled      = !show
        binding.etPassword.isEnabled   = !show
    }

    private fun goToOrders() {
        startActivity(Intent(this, OrderListActivity::class.java))
        finish()
    }
}
