//-----------------------------
// Copy citation to clipboard
//-----------------------------

jQuery(document).ready(function ($) {
    $('.copy-citation').on('click', function () {
        // Get the target citation container
        let targetId = $(this).data('target');

        // Clone the container
        let fullContent = $('#' + targetId).clone();
        
        // Remove unwanted parts
        fullContent.find('.details').remove();
        let filteredContent = fullContent.text()
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
    let citation = { author: [], keywords: [], creator: []}; 

    function formatDate(dateString) {
        return dateString ? dateString.replace(/\//g, "-") : "Unknown";
    }

    for (let i = 0; i < metas.length; i++) {
        const name = metas[i].getAttribute('name');
        if (name && name.substring(0, 9) === 'citation_') {
            const key = name.substring(9);
            let content = metas[i].getAttribute('content');
            content = content.replace(/[‘’]/g, "'").trim();
            if (key === 'pdf_url') {
                citation['url'] = content;
                const serveIndex = content.indexOf('serve') + 5;
                const baseUrl = content.substring(0, serveIndex);
                citation['hasPart'] = `${baseUrl}?render=archive`;
            }
            else if (key === 'author') {
                citation.author.push(content);
            }
            else if (key === 'online_date' || key === 'publication_date') {
                citation[key] = formatDate(content);
            }
            else {
                citation[key] = content;
            }
        }
        else if (name && name.substring(0, 8) === 'dcterms.') {
            const key = name.substring(8);
            let content = metas[i].getAttribute('content');
            content = content.replace(/[‘’]/g, "'");
            if (key === 'creator') {
                citation.creator.push(content);
            }
            else {
                citation[key] = content;
            }
        }
        else if (name && name.substring(0, 8) === 'dc.') {
            const key = name.substring(8);
            let content = metas[i].getAttribute('content');
            content = content.replace(/[‘’]/g, "'");
            citation[key] = content;
        }
        else if (name && name.substring(0, 8) === 'keywords') {
            citation['keywords'] = metas[i].getAttribute('content')
                .split(",")
                .map(k => k.trim())
                .filter(k => k.length > 0);
        }
    }

    if (format === 'json') {
        const reorderedCitation = {};
        Object.keys(citation)
            .filter(key => key !== 'keywords' && key !== 'hasPart')
            .forEach(key => {
                reorderedCitation[key] = citation[key];
            });
        reorderedCitation["keywords"] = citation["keywords"];
    
        return JSON.stringify(reorderedCitation, null, 2);
    } else if (format === 'csv') {
        const citationForCSV = Object.fromEntries(
            Object.entries(citation).filter(([key]) => key !== 'hasPart')
        );
    
        return convertToCSV(citationForCSV);
    } else if (format === 'rocrate') {
        return convertToRoCrate(citation);
    }

    return "";
}

//-----------------------------
// Conversion
//-----------------------------

function formatAuthors(authors) {
    if (!Array.isArray(authors) || authors.length === 0) return [{ "name": "Unknown" }];
    return authors.map(author => ({ "name": author.trim() }));
}

function convertToRoCrate(obj) {
    let authorsFormatted = formatAuthors(obj.author);

    const roCrate = {
        "@context": "https://w3id.org/ro/crate/1.1/context",
        "@graph": [
            {
                "@type": "CreativeWork",
                "@id": "ro-crate-metadata.jsonld",
                "conformsTo": { "@id": "https://w3id.org/ro/crate/1.1" },
                "about": { "@id": "./" }
            },
            {
                "@id": "./",
                "identifier": obj.doi || obj.identifier || "N/A",
                "@type": "Dataset",
                "name": obj.title || "No title",
                "description": obj.description || "No description provided",
                "datePublished": obj.online_date || obj.publication_date || "N/A",
                "author": authorsFormatted
            }
        ]
    };

    if (obj.license) {
        roCrate["@graph"][1]["license"] = { "@id": obj.license };

        roCrate["@graph"].push({
            "@id": obj.license,
            "@type": "CreativeWork",
            "name": obj.license,
            "description": "N/A"
        });
    }

    if (obj.hasPart) {
        roCrate["@graph"].push({
            "@id": obj.hasPart,
            "@type": "File",
            "name": obj.title || "No title",
            "url": obj.url || "No URL",
            "encodingFormat": "application/zip"
        });

        roCrate["@graph"][1]["hasPart"] = [{ "@id": obj.hasPart }];
    }

    if (Array.isArray(obj.keywords) && obj.keywords.length > 0) {
        roCrate["@graph"].push({
            "keywords": obj.keywords
        });
    }

    return JSON.stringify(roCrate, null, 2);
}

function convertToCSV(obj) {
    const keys = Object.keys(obj); 
    const values = keys.map(key => {
        let value = obj[key];

        if (Array.isArray(value)) {
            value = value.map(v => v.replace(/"/g, '""')).join("; ");
        }
        if (typeof value === 'string' && (value.includes(',') || value.includes(';'))) {
            return `"${value}"`;
        }
        return value;
    });
    const csv = keys.join(',') + '\n' + values.join(',');
    return csv;
}
