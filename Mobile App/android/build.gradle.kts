buildscript {
    repositories {
        google()
        mavenCentral()
    }
    dependencies {
        // AGP version managed by settings.gradle.kts (8.9.1)
        classpath("com.google.gms:google-services:4.4.2")
    }
}

allprojects {
    repositories {
        google()
        mavenCentral()
    }
}

val newBuildDir: Directory = rootProject.layout.buildDirectory.dir("../../build").get()
rootProject.layout.buildDirectory.value(newBuildDir)

subprojects {
    val newSubprojectBuildDir: Directory = newBuildDir.dir(project.name)
    project.layout.buildDirectory.value(newSubprojectBuildDir)

    afterEvaluate {
        extensions.findByType<com.android.build.gradle.BaseExtension>()?.apply {
            ndkVersion = "27.0.12077973"

            // Fix for AGP 8.x: auto-set namespace from AndroidManifest.xml
            // for older plugins that don't declare it in build.gradle
            if (namespace.isNullOrEmpty()) {
                val manifestFile = file("src/main/AndroidManifest.xml")
                if (manifestFile.exists()) {
                    val manifest = manifestFile.readText()
                    val packageRegex = Regex("""package\s*=\s*"([^"]+)"""")
                    val match = packageRegex.find(manifest)
                    if (match != null) {
                        namespace = match.groupValues[1]
                    }
                }
            }

            // Fix JVM target for older plugins: ensure Java and Kotlin use same target
            compileOptions {
                sourceCompatibility = JavaVersion.VERSION_11
                targetCompatibility = JavaVersion.VERSION_11
            }
        }

        // Fix Kotlin JVM target to match Java 11 for all subprojects
        tasks.withType<org.jetbrains.kotlin.gradle.tasks.KotlinCompile>().configureEach {
            kotlinOptions {
                jvmTarget = "11"
            }
        }
    }
}

subprojects {
    project.evaluationDependsOn(":app")
}

tasks.register<Delete>("clean") {
    delete(rootProject.layout.buildDirectory)
}