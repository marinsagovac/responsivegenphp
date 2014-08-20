# HTML responsive generator documentation

A quick generating HTML files using MySQL resources with next features:
* generate responsive menu for mobile, tablet and desktop using only CSS
* MySQL prepared schema with menus (parent-child nesting) and content of API documentation
* Integrated custom bb_code tags
* CSS styling, code styles with normal and warn code, header styles, text styles and subtitles
* Custom font using Google fonts local only
* A generating HTML only document using generator.php file and automatically overwrites
* Check for folder and permission of generating HTML documentation
* 3 way menu tree (main menu, submenu and section menu)

## NOTE
I haven't too many times to writing beauty code of 'apidocgen' or object orientation style because I didn't predict how exactly would be work this project.

### NOTE TO USERS
If you have more time to improve this repository to make users understand and make easier life simply fork my project.

## Mobile Users (Android)
This project is often used for Android users to make 3-way menu tree with main menu, submenus and section menus.
A plan for this project is making quikly HTML generated files for reading documentation on mobile and tablets PC.
It's covered for Desktop users too.

## HOW TO USE
Go to 'apidocgen' folder and execute php file (via php-cli or HTTP) and it will automatically generated files in generated folder.
A schema and MySQL structure is in a rel folder so you need put contents on it.
After that copy all files from generated directory to Eclipse on Android development into assets folder.
For testing purpose you can execute on your browser, simply open index.htm.

## Eclipse import from generated directory
Copy from generated directory to assets into Eclipse Android project.
This is neccessary code for Android development to execute javascript and other essential functions:
An index.htm is your root path from assets directory on Android project. 
Just copy from generated apidoc project into assets into Eclipse.
Enable on mobile USB debugging if you use external device testing or just run using emulator.
Run a project and it will show into Android device.

```
		WebView lWebView = (WebView)findViewById(R.id.webview);
		WebSettings webSettings = lWebView.getSettings();
		webSettings.setBuiltInZoomControls(true); 
		webSettings.setSupportZoom(false);
		webSettings.setJavaScriptEnabled(true);
		webSettings.setJavaScriptCanOpenWindowsAutomatically(true);   
		webSettings.setAllowFileAccess(true);
		webSettings.setDomStorageEnabled(true);
		lWebView.loadUrl("file:///android_asset/index.htm");
```

## Contact
You can directly contact me on marin[at]sagovac.com for more information.
My [stackoverflow.com](http://stackoverflow.com/users/1195557/marin-sagovac) profile: 