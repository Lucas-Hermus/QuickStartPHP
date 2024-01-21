function url(path) {
    // Get the current base URL (including protocol)
    const currentBaseUrl = window.location.origin;

    // Remove any trailing slashes from the current base URL and leading slashes from the path
    const cleanedBaseUrl = currentBaseUrl.replace(/\/+$/, ''); // Remove trailing slashes
    const cleanedPath = path.replace(/^\/+/, ''); // Remove leading slashes

    // Combine the cleaned base URL and path to form the complete URL
    const completeUrl = cleanedBaseUrl + '/' + cleanedPath;

    return completeUrl;
}

async function sendRequest(route, method = 'GET', headers = {}, body = {}) {
    try {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                ...headers,
            },
            body: method !== 'GET' ? JSON.stringify(body) : undefined,
        };

        const response = await fetch(route, options);

        if (!response.ok) {
            throw new Error(`Request failed with status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}