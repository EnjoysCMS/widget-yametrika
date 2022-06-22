grid.on('change', function (e, items) {
    window.dispatchEvent(new Event('resize'));
});

const fetchData = async function (url, data = '') {
    let response = await fetch(url, {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        headers: {
            'Content-Type': 'application/json'
        },
        referrerPolicy: 'no-referrer',
        body: JSON.stringify(data)
    }).catch(function (msg) {
        throw new Error(msg)
    });

    let json = await response.json().catch(function (msg) {
        throw new Error(msg)
    });

    if (!response.ok) {
        throw new Error(json)
    }

    return json;
}
