package com.meditrack.kurir.utils

import android.content.Context
import android.content.SharedPreferences

class SharedPrefManager private constructor(context: Context) {

    private val prefs: SharedPreferences =
        context.getSharedPreferences(Constants.PREF_NAME, Context.MODE_PRIVATE)

    companion object {
        @Volatile private var instance: SharedPrefManager? = null

        fun getInstance(context: Context): SharedPrefManager =
            instance ?: synchronized(this) {
                instance ?: SharedPrefManager(context.applicationContext).also { instance = it }
            }
    }

    var token: String?
        get() = prefs.getString(Constants.PREF_TOKEN, null)
        set(value) = prefs.edit().putString(Constants.PREF_TOKEN, value).apply()

    var userName: String?
        get() = prefs.getString(Constants.PREF_USER_NAME, null)
        set(value) = prefs.edit().putString(Constants.PREF_USER_NAME, value).apply()

    var userEmail: String?
        get() = prefs.getString(Constants.PREF_USER_EMAIL, null)
        set(value) = prefs.edit().putString(Constants.PREF_USER_EMAIL, value).apply()

    var courierId: Int
        get() = prefs.getInt(Constants.PREF_COURIER_ID, -1)
        set(value) = prefs.edit().putInt(Constants.PREF_COURIER_ID, value).apply()

    val isLoggedIn: Boolean get() = !token.isNullOrEmpty()

    fun clear() = prefs.edit().clear().apply()
}
