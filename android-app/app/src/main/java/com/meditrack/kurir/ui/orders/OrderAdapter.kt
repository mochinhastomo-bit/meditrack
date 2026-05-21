package com.meditrack.kurir.ui.orders

import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.CheckBox
import android.widget.TextView
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.meditrack.kurir.R
import com.meditrack.kurir.data.model.Order
import com.meditrack.kurir.databinding.ItemOrderBinding

sealed class OrderListItem {
    data class Header(val title: String) : OrderListItem()
    data class OrderItem(val order: Order) : OrderListItem()
}

class OrderAdapter(
    private val onItemClick: (Order) -> Unit,
    private val onCheckedChange: (Int) -> Unit
) : ListAdapter<OrderListItem, RecyclerView.ViewHolder>(DiffCallback()) {

    var selectedIds: Set<Int> = emptySet()
        set(value) {
            field = value
            notifyDataSetChanged()
        }

    companion object {
        private const val TYPE_HEADER = 0
        private const val TYPE_ORDER  = 1
    }

    override fun getItemViewType(position: Int) = when (getItem(position)) {
        is OrderListItem.Header    -> TYPE_HEADER
        is OrderListItem.OrderItem -> TYPE_ORDER
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        return if (viewType == TYPE_HEADER) {
            val view = LayoutInflater.from(parent.context)
                .inflate(R.layout.item_order_header, parent, false)
            HeaderViewHolder(view)
        } else {
            val binding = ItemOrderBinding.inflate(LayoutInflater.from(parent.context), parent, false)
            OrderViewHolder(binding)
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        when (val item = getItem(position)) {
            is OrderListItem.Header    -> (holder as HeaderViewHolder).bind(item)
            is OrderListItem.OrderItem -> (holder as OrderViewHolder).bind(item.order)
        }
    }

    inner class HeaderViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        private val tvTitle: TextView = view.findViewById(R.id.tvSectionTitle)
        fun bind(header: OrderListItem.Header) { tvTitle.text = header.title }
    }

    inner class OrderViewHolder(private val binding: ItemOrderBinding)
        : RecyclerView.ViewHolder(binding.root) {

        fun bind(order: Order) {
            binding.tvNomorResep.text  = order.nomorResep
            binding.tvPatientName.text = order.patient?.name ?: "-"
            binding.tvAddress.text     = order.address?.let { "${it.label} — ${it.address}" } ?: "-"
            binding.tvTanggal.text     = order.tanggal
            binding.tvStatus.text      = order.statusLabel

            val (bgColor, textColor) = statusColors(order.status)
            binding.tvStatus.setBackgroundColor(Color.parseColor(bgColor))
            binding.tvStatus.setTextColor(Color.parseColor(textColor))

            // Checkbox hanya tampil untuk siap_kirim
            val isSelectable = order.status == "siap_kirim"
            binding.checkbox.visibility = if (isSelectable) View.VISIBLE else View.GONE
            if (isSelectable) {
                binding.checkbox.isChecked = order.id in selectedIds
                binding.checkbox.setOnClickListener { onCheckedChange(order.id) }
            }

            // Label "Pilih →" hanya untuk selain siap_kirim, atau saat tidak ada yang dipilih
            binding.tvPilih.visibility = if (isSelectable && selectedIds.isNotEmpty()) View.GONE else View.VISIBLE

            binding.root.setOnClickListener {
                if (isSelectable) onCheckedChange(order.id)
                else onItemClick(order)
            }
        }

        private fun statusColors(status: String): Pair<String, String> = when (status) {
            "siap_kirim"       -> "#E8F0FE" to "#1557B0"
            "dibawa"           -> "#E6F4EA" to "#137333"
            "dalam_pengiriman" -> "#F3E8FD" to "#7627BB"
            else               -> "#F1F3F4" to "#5F6368"
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<OrderListItem>() {
        override fun areItemsTheSame(old: OrderListItem, new: OrderListItem): Boolean {
            return when {
                old is OrderListItem.Header && new is OrderListItem.Header -> old.title == new.title
                old is OrderListItem.OrderItem && new is OrderListItem.OrderItem -> old.order.id == new.order.id
                else -> false
            }
        }
        override fun areContentsTheSame(old: OrderListItem, new: OrderListItem) = old == new
    }
}
