# Android App

This is a first-pass Android wrapper for the existing Laravel system.

It does not use a dedicated mobile API yet. Instead, it opens the deployed web app in a mobile WebView so users can log in and use the system from Android devices with a simpler entry point.

## Before Building

1. Deploy the Laravel app to a public HTTPS URL.
2. Update `APP_BASE_URL` in [gradle.properties](./gradle.properties).

Example:

```properties
APP_BASE_URL=https://apmcb.com
```

## Open In Android Studio

Open the `android` folder as a separate project in Android Studio.

## Build

Open the `android` folder in Android Studio and let it sync the Gradle project, then build a debug APK from there.

You can also build from terminal on Windows:

```powershell
.\gradlew.bat assembleDebug
```

## Current Capabilities

- Opens the live APMCB system in a WebView
- Pull-to-refresh
- Back button support
- Basic file chooser support for upload fields
- Basic offline/error state

## Current Limitations

- This is not a full native app yet
- Existing web pages still control the actual UX
- Push notifications, offline sync, and native auth are not implemented

## Recommended Next Step

If this app will be used heavily by non-technical users, the next proper step is to expose dedicated mobile API endpoints from Laravel and build native Android screens for:

- Login
- Dashboard
- Profile
- Time off
- Cash advances
- Notifications
