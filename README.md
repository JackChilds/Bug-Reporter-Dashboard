# Bug Reporter Dashboard
A flexible and customizable dashboard for [bug reporter](https://github.com/JackChilds/Bug-Reporter).

# Usage
To use this project, just copy the dashboard/ directory into your PHP server, then edit the `preferences.json` file to suit your needs.

**Note:** if you don't have a PHP server, you may find it easier to use the dashboard in the [bug reporter](https://github.com/JackChilds/Bug-Reporter) project, however you won't be able to customize the dashboard with the `preferences.json` file and send files to the dashboard via POST requests.

## Advanced Options

### detail-highlight
The `detail-highlight` option allows you to highlight features of the bug report at the top of the report page. For example, the default value for this highlights the *userAgent* and the *screenWidth* property.

#### Syntax
A HTML string to be inserted into the report. Wrap bug properties in double curly braces.
**Bug properties example**
```html
<b>User Agent: </b> {{ data.navigatorInfo.userAgent }}
```
When referencing bug properties, use the syntax: `{{ data.property.property }}`, make sure you start with `data.` and then use the dot notation to reference the property in the same way you would if taking the bug report object in JS.
For reference to the properties in the bug report object, see the [bug reporter usage section](https://github.com/JackChilds/Bug-Reporter#usage).

## Sending data to the dashboard through a POST request
```html
<form action="path/to/dashboard/" method="post" enctype="multipart/form-data">
    <input type="text" name="hide-alert" hidden>
    <input type="text" name="preferences" value="preferences.json" hidden>

    <input type="file" name="report" accept="application/JSON" required>

    <input type="submit" value="Go to dashboard">
</form>
```
### POST parameters
**Note:** in order to submit a file to the server, you must ensure that `enctype` is set to `multipart/form-data`.

- `report` (*required*): a JSON bug report file selected by the user. **Default:** none, an error will be thrown if wrong file type or not sent at all. See [error reporting](#error-reporting) section for more info.
- `hide-alert` (*optional*): when this parameter is sent to the dashboard, the alert saying that the bug report has loaded will be hidden. **Default:** alert is shown.
- `preferences` (*optional*): specify the preferences JSON file (as a URL relative to location of dashboard file) that the dashboard should use to customize the experience. If not set the dashboard will look for the `preferences.json` file in the same directory specified directory. **Default:** not set, `preferences.json` is looked for in the dashboard directory.

## Error reporting
Currently, there are 2 errors that can be thrown to a URL (over GET). **Note:** you cannot get an error thrown to a URL when the preferences file is not available, instead the dashboard will throw an error saying `Preferences file not found` or `Custom preferences file not found`.

To set error URLs edit the `error` section of the `preferences.json` file. URLS should be relative to the dashboard directory and in GET format.

- `file-not-sent`: this URL is navigated to when the `report` parameter is not sent
- `file-not-valid`: this URL is navigated to when the `report` parameter is not a JSON file