<?php

$bug_report_file;

function readPreferences() {
    // open the preferences file and read from it, then parse as json
    $preferencesFile = fopen("preferences.json", "r");
    $preferences = fread($preferencesFile, filesize("preferences.json"));
    fclose($preferencesFile);
    return json_decode($preferences, true);
}
$preferences = readPreferences();

if(!file_exists($_FILES['report']['tmp_name']) || !is_uploaded_file($_FILES['report']['tmp_name'])) {
    header('Location: ' . $preferences['error']['file-not-sent']);
} else {
    // check if file is json
    $file = fopen($_FILES['report']['tmp_name'], "r");
    $fileContents = fread($file, filesize($_FILES['report']['tmp_name']));
    fclose($file);
    if(!json_decode($fileContents)) {
        header('Location: ' . $preferences['error']['file-not-valid']);
    } else {
        $bug_report_file = base64_encode($fileContents);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- The Bug Report File -->
    <script>
        const bug_report_file = JSON.parse(atob('<?php echo $bug_report_file; ?>'));
        
    </script>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($preferences['preferences']['page-title']); ?></title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.1/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <?php 
        $css = $preferences['css'];
        foreach ($css as $file) {
            echo '<link rel="stylesheet" href="' . $file . '">';
        }
    ?>

    <!-- Fonts -->
    <?php
        $fonts = $preferences['fonts'];
        foreach ($fonts as $file) {
            echo '<link rel="stylesheet" href="' . $file . '">';
        }
    ?>

    <!-- Sweetalert2 Theme -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($preferences['sweetalert2-theme']); ?>" crossorigin="anonymous">

    <!-- Highlight.js Base16/Snazzy Theme -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($preferences['highlightjs-theme']); ?>">

    <style>
        body {
            background-color: <?php echo $preferences['body-style']['background-color']; ?>;
            color: <?php echo $preferences['body-style']['color']; ?>;
            font-family: <?php echo $preferences['body-style']['font-family']; ?>;
            font-size: <?php echo $preferences['body-style']['font-size']; ?>;
        }
    </style>
</head>
<body>
    <div class="container p-2 mt-3">
        <div class="row p-4 shadow-sm rounded">
            <div class="col">
                <h1 class="display-6"><?php echo htmlspecialchars($preferences['preferences']['page-title']); ?></h1>
                <p class="text-muted cursor-pointer" onclick="linkToProfile()"><?php echo htmlspecialchars($preferences['preferences']['title-secondary-text']) ?></p>
                <button class="btn btn-success" onclick="uploadBugReport()">Upload Bug Report</button>
            </div>
            <div class="col text-end">
                <?php 
                    // company name
                    echo '<span class="company-name" style="font-family: ' . $preferences['preferences']['company-name']['font-family'] . '; font-size: ' . $preferences['preferences']['company-name']['font-size'] . ';">' . htmlspecialchars($preferences['preferences']['company-name']['text']) . '</span>';
                ?>
            </div>
        </div>

        <div class="row pt-1" id="please-upload-notice">
            <div class="col p-4 mt-4 mb-4 shadow rounded pos-relative">
                <span class="center">Please upload a bug report to view it here.</span>
            </div>
        </div>

        <div class="row pt-1" id="bugreport-container" style="display:none">
            <div class="col p-4 mt-4 mb-4 shadow rounded" id="bugreport-viewer">
                <h2 class="filename">Viewing: </h2>
                <h2 class="dateTime">Created: </h2>

                <h5 class="mt-3">Report generated from</h5>
                <div id="report-generated-from-info"></div>

                <!-- Detail Highlight (optional) -->
                <div id="detail-highlight">
                    <?php
                        $detailHighlight = $preferences['advanced']['detail-highlight'];
                        foreach ($detailHighlight as $l) {
                            echo '<p class="bug-property">' . $l . '</p>';
                        }
                    ?>
                </div>   


                <div class="accordion accordion-flush mt-3" id="bugreport-viewer-accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne">
                                Page Snapshot and HTML
                            </button>
                        </h2>
                        <div id="flush-collapseOne" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingOne" data-bs-parent="#bugreport-viewer-accordion">
                            <div class="accordion-body">
                                <button class="btn btn-dark" onclick="openImageModal()" id="view-page-snapshot-btn">View Page Snapshot</button>
                                <button class="btn btn-dark" onclick="downloadHTMLCode()"><i class="bi bi-download"></i> Download HTML Code</button>
                                <h5 class="mt-3">HTML Code:</h5>
                                <pre class="language-html p-2" id="html-code"></pre>

                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                aria-controls="flush-collapseTwo">
                                Console Logs
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingTwo" data-bs-parent="#bugreport-viewer-accordion">
                            <div class="accordion-body">
                                <div id="console-log-table-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseThree" aria-expanded="false"
                                aria-controls="flush-collapseThree">
                                Cookies, Local Storage and Session Storage
                            </button>
                        </h2>
                        <div id="flush-collapseThree" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingThree" data-bs-parent="#bugreport-viewer-accordion">
                            <div class="accordion-body">
                                <h5>Cookies</h5>
                                <div id="cookies-table-container"></div>
                                <h5 class="mt-3">Local Storage</h5>
                                <div id="local-storage-table-container"></div>
                                <h5 class="mt-3">Session Storage</h5>
                                <div id="session-storage-table-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseFour" aria-expanded="false"
                                aria-controls="flush-collapseFour">
                                Navigator and Screen Information
                            </button>
                        </h2>
                        <div id="flush-collapseFour" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingFour" data-bs-parent="#bugreport-viewer-accordion">
                            <div class="accordion-body">
                                <h5 class="mt-3">Navigator Information</h5>
                                <div id="navigator-info-table-container"></div>
                                <h5 class="mt-3">Screen Information</h5>
                                <div id="screen-info-table-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseFive" aria-expanded="false"
                                aria-controls="flush-collapseFive">
                                Additional Information
                            </button>
                        </h2>
                        <div id="flush-collapseFive" class="accordion-collapse collapse"
                            aria-labelledby="flush-headingFive" data-bs-parent="#bugreport-viewer-accordion">
                            <div class="accordion-body">
                                <div id="additional-info-table-container"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FileSaver.js used to download JSON file which can then be read from the dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/file-saver@2.0.5/dist/FileSaver.min.js" integrity="sha256-xoh0y6ov0WULfXcLMoaA6nZfszdgI8w2CEJ/3k8NBIE=" crossorigin="anonymous"></script>

    <!-- Sweetalert2 to handle alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.min.js" integrity="sha256-OVxeY1nP2DXp15LcHll2UDTcwaqvHlJ3xj1CjVLqvsY=" crossorigin="anonymous"></script>

    <!-- Bootstrap 5 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- HighlightJS to highlight code -->
    <script src="https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.3.1/build/highlight.min.js"></script>

    <!-- Use JS Beautify to make the HTML code prettier -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify.min.js" type="module"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify-css.min.js" type="module"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.14.0/beautify-html.min.js" type="module"></script>



    <!-- Custom JS -->
    <?php 
        $js = $preferences['js'];
        foreach ($js as $file) {
            echo '<script src="' . $file . '"></script>';
        }
    ?>
</body>
</html>