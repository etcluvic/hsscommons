//-----------------------------
// Copy citation to clipboard
//-----------------------------

jQuery(document).ready(function ($) {
    $('.copy-citation').on('click', function () {
        // Get the target citation container
        var targetId = $(this).data('target');

        // Clone the container
        var fullContent = $('#' + targetId).clone();
        
        // Remove unwanted parts
        fullContent.find('.details').remove();
        var filteredContent = fullContent.text()
            .replace('Researchers should cite this work as follows:', '')
            .trim();

        // Use Clipboard API to copy the filtered content
        navigator.clipboard.writeText(filteredContent)
            .then(function () {
                alert('Citation copied to clipboard!');
            })
            .catch(function (err) {
                console.error('Error copying citation: ', err);
            });
    });
});

//-----------------------------
// Export citation to JSON
//-----------------------------

// Function to trigger download of JSON file
function downloadJSONFile(jsonString, filename) {
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

jQuery(document).ready(function ($) {
    $('.export-jsoncitation').on('click', function () {

        // Get the JSON citation
        const citation_json = getMeta('json');

        // Download JSON file
        downloadJSONFile(citation_json, 'citation.json');
    });
});

//-----------------------------
// Export citation to CSV
//-----------------------------

// Function to trigger download of CSV file
function downloadCSVFile(csvString, filename) {
    const blob = new Blob([csvString], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}


jQuery(document).ready(function ($) {
    $('.export-csvcitation').on('click', function () {

        // Get the CSV citation
        const citation_csv = getMeta('csv');

        // Download CSV file
        downloadCSVFile(citation_csv, 'citation.csv');
    });
});

//-----------------------------
// Export citation to Ro-Crate
//-----------------------------

// Function to trigger download of Ro-Crate JSON-LD file
function downloadRoCrateFile(jsonString, filename) {
    const blob = new Blob([jsonString], { type: 'application/ld+json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

jQuery(document).ready(function ($) {
    $('.export-rocratecitation').on('click', function () {

        // Get the Ro-Crate metadata
        const citation_rocrate = getMeta('rocrate');

        // Download Ro-Crate file
        downloadRoCrateFile(citation_rocrate, 'citation-rocrate.jsonld');
    });
});

//-----------------------------
// Parse Metadata
//-----------------------------

function getMeta(format) {
    const metas = document.getElementsByTagName('meta');
    var citation = {};

    for (let i = 0; i < metas.length; i++) {
        const name = metas[i].getAttribute('name');
        if (name && name.substring(0, 9) === 'citation_') {
            const key = name.substring(9);
            var content = metas[i].getAttribute('content');
            content = content.replace(/[‘’]/g, "'"); // Fix apostrophes
            citation[key] = content;
        }
    }

    if (format === 'json') {
        return JSON.stringify(citation, null, 2);
    } else if (format === 'csv') {
        return convertToCSV(citation);
    } else if (format === 'rocrate') {
        return convertToRoCrate(citation);
    }

    return "";
}

function convertToRoCrate(obj) {
    const roCrate = {
        "@context": "https://w3id.org/ro/crate/1.1/context",
        "@graph": [
            {
                "@id": "./",
                "@type": "Dataset",
                "name": obj.title || "Citation",
                "creator": obj.author || "Unknown",
                "description": obj.abstract || "No description provided",
                "keywords": obj.keywords ? obj.keywords.split(",") : [],
                "datePublished": obj.year || "Unknown",
                "identifier": obj.doi || "No DOI",
            },
        ],
    };
    return JSON.stringify(roCrate, null, 2);
}

function convertToCSV(obj) {
    const keys = Object.keys(obj);
    const values = Object.values(obj).map(value => {
        if (typeof value === 'string' && value.includes(',')) {
            value = value.replace(/"/g, '""'); // Escape double quotes
            return `"${value}"`; // Wrap value in double quotes
        }
        return value;
    });
    const csv = keys.join(',') + '\n' + values.join(',');
    return csv;
}