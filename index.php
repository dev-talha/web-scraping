<?php
// Function to fetch the HTML content of a webpage
function fetchHTML(string $url): string {
    $html = file_get_contents($url);
    return $html;
}

// Function to extract all relevant data from HTML
function extractData(string $html, int $page, int $perPage): array {
    $doc = new DOMDocument();
    // Suppress warnings for invalid HTML
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $xpath = new DOMXPath($doc);
    $data = [];
    // Calculate start and end position for pagination
    $start = ($page - 1) * $perPage;
    $end = $start + $perPage;
    // Extract data elements within pagination range
    $elements = $xpath->query("//a[contains(@class, 'text-dark')]");
    for ($i = $start; $i < $end; $i++) {
      if ($elements->item($i)) {
        $item = [
            'text' => $elements->item($i)->nodeValue,
            'href' => $elements->item($i)->getAttribute('href'),
        ];
        $data[] = $item;
    } else {
        break; // No more elements left
    }
    }
    return $data;
}

// Main function to retrieve and print paginated data
function retrieveAndPrintPaginatedData(int $page = 1, int $perPage = 10): void {
    $url = "http://pundrauniversity.ac.bd/notice";
    $html = fetchHTML($url);
    $data = extractData($html, $page, $perPage);
    // Print paginated data with item index number
    $index = ($page - 1) * $perPage + 1;
    foreach ($data as $item) {
        echo  $index++ . ". <a href=http://pundrauniversity.ac.bd{$item['href']}>".$item['text'] ."</a>";
        echo "<br><br>";
    }
    // Print pagination links (basic)
    echo "Page $page\n";
    if ($page > 1) {
        echo "<a href='?page=".($page-1)."'>Previous</a> ";
    }
    echo "<a href='?page=".($page+1)."'>Next</a>";
}

// Example usage
$page = isset($_GET['page']) ? max(1, $_GET['page']) : 1; // Get current page number
$perPage = 20; // Items per page
echo "Data (Page $page):\n";
retrieveAndPrintPaginatedData($page, $perPage);
?>
