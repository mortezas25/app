package com.example.mywebviewapp;

import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;

public class MainActivity extends AppCompatActivity {
    private WebView webView;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        webView = findViewById(R.id.webview);
        
        // تنظیمات وب ویو
        WebSettings webSettings = webView.getSettings();
        webSettings.setJavaScriptEnabled(true); // فعال سازی جاوااسکریپت
        
        // جلوگیری از باز شدن لینک‌ها در مرورگر خارجی
        webView.setWebViewClient(new WebViewClient());
        
        // بارگذاری وب سایت
        webView.loadUrl("https://avayzaravand.ir/h403/");
    }

    // مدیریت دکمه بازگشت برای بازگشت در تاریخچه وب ویو
    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            super.onBackPressed();
        }
    }
}
