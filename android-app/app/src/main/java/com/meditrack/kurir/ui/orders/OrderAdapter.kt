package com.meditrack.kurir.ui.orders

import android.graphics.Color
import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.meditrack.kurir.data.model.Order
import com.meditrack.kurir.databinding.ItemOrderBinding

class OrderAdapter(
    private val onItemClick: (Order) -> Unit
) : ListAdapter<Order, OrderAdapter.ViewHolder>(DiffCallback()) {

    inner class ViewHolder(private val binding: ItemOrderBinding)
        : RecyclerView.ViewHolder(binding.root) {

        fun bind(order: Order) {
            binding.tvNomorResep.text  = order.nomorResep
            binding.tvPatientName.text = order.patient?.name ?: "-"
            binding.tvAddress.text     = order.address?.let { "${it.label} — ${it.address}" } ?: "-"
            binding.tvTanggal.text     = order.tanggal
            binding.tvStatus.text      = order.statusLabel

            // Warna status
            val bgColor = when (order.status) {
                "siap_kirim"       -> "#E8F0FE"
                "dalam_pengiriman" -> "#F3E8FD"
                else               -> "#F1F3F4"
            }
            val textColor = when (order.status) {
                "siap_kirim"       -> "#1557B0"
                "dalam_pengiriman" -> "#7627BB"
                else               -> "#5F6368"
            }
            binding.tvStatus.setBackgroundColor(Color.parseColor(bgColor))
            binding.tvStatus.setTextColor(Color.parseColor(textColor))

            binding.root.setOnClickListener { onItemClick(order) }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemOrderBinding.inflate(
            LayoutInflater.from(parent.context), parent, false
        )
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    class DiffCallback : DiffUtil.ItemCallback<Order>() {
        override fun areItemsTheSame(old: Order, new: Order) = old.id == new.id
        override fun areContentsTheSame(old: Order, new: Order) = old == new
    }
}
