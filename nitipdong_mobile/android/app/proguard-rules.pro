# Flutter Wrapper Proguard Rules
-keep class io.flutter.app.** { *; }
-keep class io.flutter.plugin.**  { *; }
-keep class io.flutter.util.**  { *; }
-keep class io.flutter.view.**  { *; }
-keep class io.flutter.**  { *; }
-keep class io.flutter.plugins.**  { *; }

# Preserve Google Fonts & Networking
-dontwarn com.google.android.gms.**
-dontwarn javax.annotation.**
-dontwarn org.bouncycastle.**
-keepattributes *Annotation*
-keepattributes Signature
