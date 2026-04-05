import java.util.Properties
import java.io.FileInputStream

// Load keystore properties
val keystoreProperties = Properties()
val keystorePropertiesFile = rootProject.file("key.properties")
if (keystorePropertiesFile.exists()) {
    FileInputStream(keystorePropertiesFile).use {
        keystoreProperties.load(it)
    }
}

// Load .env properties
val envProperties = Properties()
val envFile = rootProject.file("../.env")
if (envFile.exists()) {
    FileInputStream(envFile).use {
        envProperties.load(it)
    }
}

plugins {
    id("com.android.application")
    id("kotlin-android")
    id("dev.flutter.flutter-gradle-plugin")
}

android {
    namespace = envProperties.getProperty("ANDROID_IDENTIFIER") ?: "com.ulinmahoni.apps"
    compileSdk = flutter.compileSdkVersion
    ndkVersion = "27.0.12077973"

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_11
        targetCompatibility = JavaVersion.VERSION_11
        isCoreLibraryDesugaringEnabled = true
    }

    kotlinOptions {
        jvmTarget = JavaVersion.VERSION_11.toString()
    }

    defaultConfig {
        applicationId = envProperties.getProperty("ANDROID_IDENTIFIER") ?: "com.ulinmahoni.apps"
        minSdk = flutter.minSdkVersion
        targetSdk = flutter.targetSdkVersion
        versionCode = envProperties.getProperty("VERSION_CODE")?.toIntOrNull() ?: 29
        versionName = envProperties.getProperty("VERSION_NAME") ?: "2.0.6"
    }

    println("🚨 [DEBUG] Keystore storeFile: " + keystoreProperties["storeFile"])
    val debugKeystorePath = keystoreProperties["storeFile"]?.toString() ?: ""
    println("🚨 [DEBUG] Keystore file exists: " + file(debugKeystorePath).exists())

    signingConfigs {
        create("release") {
            val storeFilePath = keystoreProperties["storeFile"]?.toString()
            if (storeFilePath.isNullOrBlank()) {
                throw GradleException("storeFile path in key.properties is missing or null")
            }
            storeFile = file(storeFilePath)
            storePassword = keystoreProperties["storePassword"]?.toString() ?: throw GradleException("storePassword is missing")
            keyAlias = keystoreProperties["keyAlias"]?.toString() ?: throw GradleException("keyAlias is missing")
            keyPassword = keystoreProperties["keyPassword"]?.toString() ?: throw GradleException("keyPassword is missing")
        }
    }

    buildTypes {
        release {
            signingConfig = signingConfigs.getByName("release")
            isDebuggable = false
        }
        getByName("debug") {
            isDebuggable = true
        }
    }
}

flutter {
    source = "../.."
}

dependencies {
    coreLibraryDesugaring("com.android.tools:desugar_jdk_libs:2.1.4")
}

apply(plugin = "com.google.gms.google-services")
