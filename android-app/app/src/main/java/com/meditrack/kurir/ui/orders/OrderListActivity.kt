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
import com.meditrack.kurir.ui.history.HistoryActivity
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
        setupPickupBar()

        viewModel.loadOrders()
    }

    override fun onResume() {
        super.onResume()
        viewModel.loadOrders()
    }

    private fun setupToolbar() {
        binding.tvUserName.text = prefs.userName ?: "Kurir"
        binding.btnHistory.setOnClickListener {
            startActivity(Intent(this, HistoryActivity::class.java))
        }
        binding.btnLogout.setOnClickListener { confirmLogout() }
    }

    private fun setupRecyclerView() {
        adapter = OrderAdapter(
            onItemClick    = { order -> onOrderSelected(order) },
            onCheckedChange = { id -> viewModel.toggleSelection(id) }
        )
        binding.recyclerView.adapter = adapter
    }

    private fun setupObservers() {
        viewModel.orders.observe(this) { orders ->
            val items = buildSectionedList(orders)
            adapter.submitList(items)
            binding.tvEmpty.visibility = if (orders.isEmpty()) View.VISIBLE else View.GONE
        }

        viewModel.isLoading.observe(this) { loading ->
            binding.swipeRefresh.isRefreshing = loading
        }

        viewModel.error.observe(this) { error ->
            error?.let { Toast.makeText(this, it, Toast.LENGTH_LONG).show() }
        }

        viewModel.pickupSuccess.observe(this) { msg ->
            msg?.let {
                Toast.makeText(this, it, Toast.LENGTH_LONG).show()
                viewModel.clearPickupSuccess()
            }
        }

        viewModel.selectedIds.observe(this) { selected ->
            adapter.selectedIds = selected
            updatePickupBar(selected)
        }
    }

    private fun buildSectionedList(orders: List<Order>): List<OrderListItem> {
        val result = mutableListOf<OrderListItem>()

        val aktif   = orders.filter { it.status == "dalam_pengiriman" }
        val dibawa  = orders.filter { it.status == "dibawa" }
        val siapKirim = orders.filter { it.status == "siap_kirim" }
        val lainnya = orders.filter { it.status !in listOf("dalam_pengiriman", "dibawa", "siap_kirim") }

        if (aktif.isNotEmpty()) {
            result += OrderListItem.Header("SEDANG DIANTAR")
            result += aktif.map { OrderListItem.OrderItem(it) }
        }
        if (dibawa.isNotEmpty()) {
            result += OrderListItem.Header("SUDAH DIAMBIL — ANTRI ANTAR")
            result += dibawa.map { OrderListItem.OrderItem(it) }
        }
        if (siapKirim.isNotEmpty()) {
            result += OrderListItem.Header("SIAP KIRIM — KETUK UNTUK PILIH")
            result += siapKirim.map { OrderListItem.OrderItem(it) }
        }
        if (lainnya.isNotEmpty()) {
            result += OrderListItem.Header("DALAM PROSES")
            result += lainnya.map { OrderListItem.OrderItem(it) }
        }

        return result
    }

    private fun setupPickupBar() {
        binding.btnAmbilResep.setOnClickListener {
            val count = viewModel.selectedIds.value?.size ?: 0
            AlertDialog.Builder(this)
                .setTitle("Ambil Resep")
                .setMessage("Ambil $count resep sekaligus dari RS?")
                .setPositiveButton("Ya, Ambil") { _, _ -> viewModel.pickupSelected() }
                .setNegativeButton("Batal", null)
                .show()
        }
        binding.btnBatalPilih.setOnClickListener { viewModel.clearSelection() }
    }

    private fun updatePickupBar(selected: Set<Int>) {
        if (selected.isEmpty()) {
            binding.bottomPickupBar.visibility = View.GONE
        } else {
            binding.bottomPickupBar.visibility = View.VISIBLE
            binding.tvSelectedCount.text = "${selected.size} resep dipilih"
        }
    }

    private fun setupSwipeRefresh() {
        binding.swipeRefresh.setOnRefreshListener {
            viewModel.clearSelection()
            viewModel.loadOrders()
        }
    }

    private fun onOrderSelected(order: Order) {
        val address = order.address
        val intent = Intent(this, DeliveryActivity::class.java).apply {
            putExtra(Constants.EXTRA_ORDER_ID,      order.id)
            putExtra(Constants.EXTRA_ORDER_NOMOR,   order.nomorResep)
            putExtra(Constants.EXTRA_ORDER_STATUS,  order.status)
            putExtra(Constants.EXTRA_PATIENT_NAME,  order.patient?.name ?: "-")
            putExtra(Constants.EXTRA_DEST_LAT,      address?.latitude ?: 0.0)
            putExtra(Constants.EXTRA_DEST_LNG,      address?.longitude ?: 0.0)
            putExtra(Constants.EXTRA_DEST_LABEL,    address?.label ?: "-")
            putExtra(Constants.EXTRA_DEST_ADDRESS,  address?.address ?: "-")
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
