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
// Parse metadata
//-----------------------------

function getMeta(format) {
    const metas = document.getElementsByTagName('meta');
    let citation = { author: [], keywords: [], creator: [] };

    function formatDate(dateString) {
        return dateString ? dateString.replace(/\//g, "-") : "Unknown";
    }

    for (let i = 0; i < metas.length; i++) {
        const name = metas[i].getAttribute('name');
        if (name && name.startsWith('citation_')) {
            const key = name.substring(9);
            let content = metas[i].getAttribute('content').replace(/[‘’]/g, "'").trim();

            if (key === 'pdf_url') {
                citation['url'] = content;
                const serveIndex = content.indexOf('serve') + 5;
                const baseUrl = content.substring(0, serveIndex);
                citation['hasPart'] = `${baseUrl}?render=archive`;
            } else if (key === 'author') {
                citation.author.push(content);
            } else if (key === 'online_date' || key === 'publication_date') {
                citation[key] = formatDate(content);
            } else {
                citation[key] = content;
            }
        }
    }
    const jsonLdScripts = document.querySelectorAll('script[type="application/ld+json"]');

    for (let script of jsonLdScripts) {
        try {
            const jsonLdData = JSON.parse(script.innerText);

            if (jsonLdData["@type"] === "Dataset") {
                citation.title = jsonLdData.name || citation.title || "No title";

                citation.description =  jsonLdData.description || 
                                        citation.description || 
                                        document.querySelector('meta[name="dcterms.description"]')?.content || 
                                        "No description provided";

                citation.url = jsonLdData.url || citation.url || "No URL";
                citation.datePublished = jsonLdData.datePublished || jsonLdData.dateCreated || citation.datePublished || "N/A";
                citation.identifier = jsonLdData.identifier || jsonLdData["@id"] || citation.identifier || "N/A";

                if (jsonLdData.license || jsonLdData.sdLicense) {
                    citation.license = {
                        "@id": jsonLdData.sdLicense || "N/A",
                        "name": jsonLdData.license || "Unknown License"
                    };
                }
                if (Array.isArray(jsonLdData.author)) {
                    citation.author = jsonLdData.author.map(author => ({
                        "@type": "Person",
                        "givenName": author.givenName || "",
                        "familyName": author.familyName || "",
                        "url": author.url || ""
                    }));
                }
                if (jsonLdData.publisher) {
                    citation.publisher = jsonLdData.publisher.name || "Unknown";
                    citation.publisherUrl = jsonLdData.publisher.url || "";
                }
                if (Array.isArray(jsonLdData.keywords)) {
                    citation.keywords = jsonLdData.keywords.map(k => k.trim());
                }
            }
        } catch (error) {
            console.error("Error parsing JSON-LD:", error);
        }
    }

    if (format === 'json') {
        return JSON.stringify(citation, null, 2);
    } else if (format === 'csv') {
        return convertToCSV(citation)
    } else if (format === 'rocrate') {
        return convertToRoCrate(citation);
    }

    return "";
}

//-----------------------------
// Conversion for RO-Crate
//-----------------------------

function convertToRoCrate(obj) {
    let authorsFormatted = Array.isArray(obj.author) && obj.author.length > 0 ? obj.author : [{ "@type": "Person", "name": "Unknown" }];

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
                "datePublished": obj.datePublished || "N/A",
                "author": authorsFormatted
            }
        ]
    };
    if (obj.license && obj.license["@id"] !== "N/A") {
        roCrate["@graph"][1]["license"] = {
            "@id": obj.license["@id"],
            "name": obj.license["name"]
        };
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
        roCrate["@graph"][1]["keywords"] = obj.keywords;
    }

    return JSON.stringify(roCrate, null, 2);
}

//-----------------------------
// Conversion for CSV
//-----------------------------

function convertToCSV(obj) {
    const keys = Object.keys(obj);
    const values = keys.map(key => {
        let value = obj[key];
        if (key === 'author' && Array.isArray(value)) {
            value = value.map(author => {
                let fullName = `${author.familyName}, ${author.givenName}`.trim();
                return fullName.replace(/"/g, '""');
            }).join("; ");
        }
        else if (Array.isArray(value)) {
            value = value.map(v => v.replace(/"/g, '""')).join("; ");
        }
        else if (typeof value === 'object' && value !== null) {
            value = `"${value.name} (${value["@id"]})"`;
        }
        if (typeof value === 'string' && (value.includes(',') || value.includes(';'))) {
            return `"${value}"`;
        }

        return value;
    });

    const csv = keys.join(',') + '\n' + values.join(',');
    return csv;
}
