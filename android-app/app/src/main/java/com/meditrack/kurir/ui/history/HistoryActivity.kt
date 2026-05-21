package com.meditrack.kurir.ui.history

import android.graphics.Color
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.lifecycleScope
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.meditrack.kurir.data.api.RetrofitClient
import com.meditrack.kurir.data.model.Order
import com.meditrack.kurir.data.repository.OrderRepository
import com.meditrack.kurir.data.repository.Result
import com.meditrack.kurir.databinding.ActivityHistoryBinding
import com.meditrack.kurir.databinding.ItemHistoryBinding
import com.meditrack.kurir.utils.SharedPrefManager
import kotlinx.coroutines.launch

class HistoryActivity : AppCompatActivity() {

    private lateinit var binding: ActivityHistoryBinding
    private lateinit var prefs: SharedPrefManager
    private lateinit var adapter: HistoryAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        binding = ActivityHistoryBinding.inflate(layoutInflater)
        setContentView(binding.root)

        prefs   = SharedPrefManager.getInstance(this)
        adapter = HistoryAdapter()
        binding.recyclerView.adapter = adapter

        binding.btnBack.setOnClickListener { finish() }
        binding.swipeRefresh.setOnRefreshListener { loadHistory() }

        loadHistory()
    }

    private fun loadHistory() {
        binding.swipeRefresh.isRefreshing = true
        val repo = OrderRepository(RetrofitClient.getInstance(prefs))
        lifecycleScope.launch {
            when (val result = repo.getHistory()) {
                is Result.Success -> {
                    adapter.submitList(result.data)
                    binding.tvEmpty.visibility =
                        if (result.data.isEmpty()) View.VISIBLE else View.GONE
                }
                is Result.Error -> {
                    Toast.makeText(this@HistoryActivity, result.message, Toast.LENGTH_LONG).show()
                }
            }
            binding.swipeRefresh.isRefreshing = false
        }
    }
}

class HistoryAdapter : ListAdapter<Order, HistoryAdapter.VH>(Diff()) {

    inner class VH(private val binding: ItemHistoryBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(order: Order) {
            binding.tvNomorResep.text = order.nomorResep
            binding.tvPatient.text    = order.patient?.name ?: "-"
            binding.tvAddress.text    = order.address?.let { "${it.label} — ${it.address}" } ?: "-"
            binding.tvTanggal.text    = order.tanggal
            binding.tvStatus.text     = order.statusLabel

            val (bg, fg) = when (order.status) {
                "terkirim"   -> "#E6F4EA" to "#137333"
                "dibatalkan" -> "#FCE8E6" to "#C5221F"
                else         -> "#F1F3F4" to "#5F6368"
            }
            binding.tvStatus.setBackgroundColor(Color.parseColor(bg))
            binding.tvStatus.setTextColor(Color.parseColor(fg))
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int) =
        VH(ItemHistoryBinding.inflate(LayoutInflater.from(parent.context), parent, false))

    override fun onBindViewHolder(holder: VH, position: Int) = holder.bind(getItem(position))

    class Diff : DiffUtil.ItemCallback<Order>() {
        override fun areItemsTheSame(a: Order, b: Order) = a.id == b.id
        override fun areContentsTheSame(a: Order, b: Order) = a == b
    }
}
