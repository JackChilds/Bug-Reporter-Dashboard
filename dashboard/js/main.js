/*
* 
* Bug Reporter Dashboard
* Apache 2.0 License
* By Jack Childs 2021
* 
*/

const bugreportContainer = document.querySelector('#bugreport-container');
const bugreportViewer = document.querySelector('#bugreport-viewer');


function openImageModal() {
    Swal.fire({
        html: '<button class="m-2 btn btn-light" onclick="downloadPageSnapshot()"><i class="bi bi-download"></i> Download Image</button>',
        imageUrl: bugreport.pageSnapshot,
        imageWidth: 500,
        showConfirmButton: false,
    });
}
function downloadPageSnapshot() {
    saveAs(bugreport.pageSnapshot, "Page Snapshot.png");
}
function downloadHTMLCode() {
    saveAs(new Blob([bugreport.html], {type: "text/plain;charset=utf-8"}), "bugreport.html");
}

function stringify (value) {
    switch (typeof value) {
        case 'string': case 'object': return JSON.stringify(value);
        default: return String(value);
    }
}

function createTableFrom2dArray(tableData, header, str=false) {
    // create html table from 2d array
    var table = document.createElement('table');
    var tableBody = document.createElement('tbody');

    var headerRow = document.createElement('tr');
    header.forEach(function(item) {
        var cell = document.createElement('th');
        cell.appendChild(document.createTextNode(item));
        headerRow.appendChild(cell);
    });
    tableBody.appendChild(headerRow);

    tableData.forEach(function (rowData) {
        var row = document.createElement('tr');

        rowData.forEach(function (cellData) {
            var cell = document.createElement('td');
            if (str) cellData = stringify(cellData);
            cell.appendChild(document.createTextNode(cellData));
            row.appendChild(cell);
        });

        tableBody.appendChild(row);
    });

    table.appendChild(tableBody);

    table.classList.add('table');

    return table;
}

function createTableFromObject(object, header) {
    // create html table from object
    var table = document.createElement('table');
    var tableBody = document.createElement('tbody');

    var headerRow = document.createElement('tr');
    header.forEach(function(item) {
        var cell = document.createElement('th');
        cell.appendChild(document.createTextNode(item));
        headerRow.appendChild(cell);
    });
    tableBody.appendChild(headerRow);
    
    Object.keys(object).forEach(function (key) {
        var row = document.createElement('tr');

        var cell = document.createElement('td');
        cell.appendChild(document.createTextNode(key));
        row.appendChild(cell);

        cell = document.createElement('td');
        cell.appendChild(document.createTextNode(object[key]));
        row.appendChild(cell);

        tableBody.appendChild(row);
    });

    table.appendChild(tableBody);

    table.classList.add('table');

    return table;
}

function bugreportChanged() {
    bugreportViewer.querySelector('.filename').innerHTML = "Viewing: " + bugreport.filename;

    if (bugreport.windowLocation !== null) {
        bugreportViewer.querySelector('#report-generated-from-info').innerHTML = '<p>Full URL: <a class="link-secondary" href="' + bugreport.windowLocation.href + '" target="_blank">' + bugreport.windowLocation.href + '</a></p><p>Hostname: <span class="text-muted">' + bugreport.windowLocation.hostname + '</span></p><p>Host: <span class="text-muted">' + bugreport.windowLocation.host + '</span></p><p>Pathname: <span class="text-muted">' + bugreport.windowLocation.pathname + '</span></p><p>Protocol: <span class="text-muted">' + bugreport.windowLocation.protocol + '</span></p><p>Port: <span class="text-muted">' + bugreport.windowLocation.port + '</span></p>';
    } else {
        bugreportViewer.querySelector('#report-generated-from-info').innerHTML = '<p class="text-muted">No window location data available in bug report</p>';
    }

    if (bugreport.pageSnapshot === null) {
        bugreportViewer.querySelector('#view-page-snapshot-btn').setAttribute('disabled', 'disabled');
    } else {
        bugreportViewer.querySelector('#view-page-snapshot-btn').removeAttribute('disabled');
    }

    bugreportViewer.querySelector('#html-code').textContent = (bugreport.html === null ? 'No HTML content available in bug report' : html_beautify(bugreport.html));
    hljs.highlightElement(bugreportViewer.querySelector('#html-code'));
    bugreportViewer.querySelector('.dateTime').innerHTML = 'Created: <span class="text-muted">' + bugreport.dateTime + '</span>';

    let table;

    if (bugreport.consoleOutput !== null) {
        bugreportViewer.querySelector('#console-log-table-container').innerHTML = '<input type="text" placeholder="Search console log..." class="form-control" onkeyup="tableSearch(this, document.querySelector(\'#console-log-table-container table\'))">';

        table = createTableFrom2dArray(bugreport.consoleOutput, ['Date/Time', 'Message', 'Log Type']);
        bugreportViewer.querySelector('#console-log-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#console-log-table-container').innerHTML = 'No console log data available in this bug report';
    }

    if (bugreport.cookies !== null && Object.keys(bugreport.cookies).length !== 0) {
        bugreportViewer.querySelector('#cookies-table-container').innerHTML = '<input type="text" placeholder="Search cookies..." class="form-control" onkeyup="tableSearch(this, document.querySelector(\'#cookies-table-container table\'))">';
        table = createTableFromObject(bugreport.cookies, ['Name', 'Value']);
        bugreportViewer.querySelector('#cookies-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#cookies-table-container').innerHTML = 'No cookie data available in this bug report';
    }

    if (bugreport.localStorage !== null && Object.keys(bugreport.localStorage).length !== 0) {
        bugreportViewer.querySelector('#local-storage-table-container').innerHTML = '<input type="text" placeholder="Search local storage..." class="form-control" onkeyup="tableSearch(this, document.querySelector(\'#local-storage-table-container table\'))">';

        table = createTableFromObject(bugreport.localStorage, ['Name', 'Value']);
        bugreportViewer.querySelector('#local-storage-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#local-storage-table-container').innerHTML = 'No local storage data available in this bug report';
    }

    if (bugreport.sessionStorage !== null && Object.keys(bugreport.sessionStorage).length !== 0) {
        bugreportViewer.querySelector('#session-storage-table-container').innerHTML = '<input type="text" placeholder="Search session storage..." class="form-control" onkeyup="tableSearch(this, document.querySelector(\'#session-storage-table-container table\'))">';

        table = createTableFromObject(bugreport.sessionStorage, ['Name', 'Value']);
        bugreportViewer.querySelector('#session-storage-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#session-storage-table-container').innerHTML = 'No session storage data available in bug report';
    }

    if (bugreport.navigatorInfo !== null && Object.keys(bugreport.navigatorInfo).length !== 0) {
        table = createTableFromObject(bugreport.navigatorInfo, ['Property Name', 'Value']);
        bugreportViewer.querySelector('#navigator-info-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#navigator-info-table-container').innerHTML = 'No navigator information available in bug report';
    }

    if (bugreport.screenInfo !== null && Object.keys(bugreport.screenInfo).length !== 0) {
        table = createTableFromObject(bugreport.screenInfo, ['Property Name', 'Value']);
        bugreportViewer.querySelector('#screen-info-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#screen-info-table-container').innerHTML = 'No screen information available in bug report';
    }

    if (bugreport.additionalInfo !== null && bugreport.additionalInfo.length !== 0) {
        bugreportViewer.querySelector('#additional-info-table-container').innerHTML = '<input type="text" placeholder="Search additional information..." class="form-control" onkeyup="tableSearch(this, document.querySelector(\'#additional-info-table-container table\'))">';
        table = createTableFrom2dArray(bugreport.additionalInfo, ['Property Name', 'Value'], true);
        bugreportViewer.querySelector('#additional-info-table-container').appendChild(table);
    } else {
        bugreportViewer.querySelector('#additional-info-table-container').innerHTML = 'No additional information available in bug report';
    }

    bugPropertyReplace();
}

function tableSearch(input, table) {
    var searchInput = input.value.toLowerCase();
    var tableRows = table.querySelectorAll('tr');

    for (var i = 1; i < tableRows.length; i++) {
        var row = tableRows[i];
        var rowText = row.innerText.toLowerCase();

        if (rowText.indexOf(searchInput) > -1) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}


/*
* This function gets all elements with the bug-property class, then modifies 
* the element to replace bug report references with the respective values.
* 
* E.g. 
* <span class="bug-property">User agent: {{ data.navigatorInfo.userAgent }}</span>
* Becomes (value of bugreport.navigatorInfo.userAgent)
* <span class="bug-reporter">User agent: Mozillla...</span>
*/
function bugPropertyReplace() {
    var bugProperties = document.querySelectorAll('.bug-property');
    const regex = /\{\{.*?\}\}/g;

    bugProperties.forEach((e) => {
        const property = e.innerHTML.match(regex);
        if (property !== null) {
            for (let n = 0; n < property.length; n++) {
                const innerProperty = property[n]
                .replace('{{', '')
                .replace('}}', '')
                .trim();
                
                let replacement = null;

                const p = innerProperty.split('.');
                if (p[0] === 'data') {
                    let item = bugreport;
                    for (let i = 1; i < p.length; i++) {
                        item = item[p[i]];
                    }
                    replacement = item;
                }

                e.innerHTML = e.innerHTML.replace(property[n], replacement);
            }
        }
    });
}