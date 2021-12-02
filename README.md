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
    <!-- Suppress the alert when the bug report is loaded -->
    <input type="text" name="hide-alert" hidden>

    <!-- Specify preferences.json file (as a url relative to location of dashboard file). If not specified, the dashboard will look for the preferences.json file in the same directory -->
    <input type="text" name="preferences" value="preferences.json" hidden>
    <input type="file" name="report">
    <input type="submit" value="Go to dashboard">
</form>
```
