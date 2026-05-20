package com.meditrack.kurir.ui.orders

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.ViewModel
import androidx.lifecycle.ViewModelProvider
import com.meditrack.kurir.data.api.RetrofitClient
import com.meditrack.kurir.data.model.Order
import com.meditrack.kurir.data.repository.AuthRepository
import com.meditrack.kurir.data.repository.OrderRepository
import com.meditrack.kurir.databinding.ActivityOrderListBinding
import com.meditrack.kurir.ui.delivery.DeliveryActivity
import com.meditrack.kurir.ui.login.LoginActivity
import com.meditrack.kurir.utils.Constants
import com.meditrack.kurir.utils.SharedPrefManager
import kotlinx.coroutines.CoroutineScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch

class OrderListActivity : AppCompatActivity() {

    private lateinit var binding: ActivityOrderListBinding
    private lateinit var prefs: SharedPrefManager
    private lateinit var adapter: OrderAdapter

    private val viewModel: OrderListViewModel by viewModels {
        object : ViewModelProvider.Factory {
            override fun <T : ViewModel> create(modelClass: Class<T>): T {
                val api  = RetrofitClient.getInstance(prefs)
                val repo = OrderRepository(api)
                @Suppress("UNCHECKED_CAST")
                return OrderListViewModel(repo) as T
            }
        }
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityOrderListBinding.inflate(layoutInflater)
        setContentView(binding.root)

        prefs = SharedPrefManager.getInstance(this)

        setupToolbar()
        setupRecyclerView()
        setupObservers()
        setupSwipeRefresh()

        viewModel.loadOrders()
    }

    override fun onResume() {
        super.onResume()
        // Refresh list saat kembali dari DeliveryActivity
        viewModel.loadOrders()
    }

    private fun setupToolbar() {
        binding.tvUserName.text = prefs.userName ?: "Kurir"
        binding.btnLogout.setOnClickListener { confirmLogout() }
    }

    private fun setupRecyclerView() {
        adapter = OrderAdapter { order -> onOrderSelected(order) }
        binding.recyclerView.adapter = adapter
    }

    private fun setupObservers() {
        viewModel.orders.observe(this) { orders ->
            adapter.submitList(orders)
            binding.tvEmpty.visibility =
                if (orders.isEmpty()) View.VISIBLE else View.GONE
        }

        viewModel.isLoading.observe(this) { loading ->
            binding.swipeRefresh.isRefreshing = loading
        }

        viewModel.error.observe(this) { error ->
            error?.let { Toast.makeText(this, it, Toast.LENGTH_LONG).show() }
        }
    }

    private fun setupSwipeRefresh() {
        binding.swipeRefresh.setOnRefreshListener {
            viewModel.loadOrders()
        }
    }

    private fun onOrderSelected(order: Order) {
        val address = order.address
        val intent = Intent(this, DeliveryActivity::class.java).apply {
            putExtra(Constants.EXTRA_ORDER_ID,     order.id)
            putExtra(Constants.EXTRA_ORDER_NOMOR,  order.nomorResep)
            putExtra(Constants.EXTRA_PATIENT_NAME, order.patient?.name ?: "-")
            putExtra(Constants.EXTRA_DEST_LAT,     address?.latitude ?: 0.0)
            putExtra(Constants.EXTRA_DEST_LNG,     address?.longitude ?: 0.0)
            putExtra(Constants.EXTRA_DEST_LABEL,   address?.label ?: "-")
            putExtra(Constants.EXTRA_DEST_ADDRESS, address?.address ?: "-")
        }
        startActivity(intent)
    }

    private fun confirmLogout() {
        AlertDialog.Builder(this)
            .setTitle("Keluar")
            .setMessage("Apakah Anda yakin ingin keluar?")
            .setPositiveButton("Keluar") { _, _ -> doLogout() }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun doLogout() {
        CoroutineScope(Dispatchers.Main).launch {
            val api  = RetrofitClient.getInstance(prefs)
            val repo = AuthRepository(api, prefs)
            repo.logout()
            RetrofitClient.reset()
            startActivity(Intent(this@OrderListActivity, LoginActivity::class.java))
            finish()
        }
    }
}
