let variables = {};
let params = {};
const searchKey = $('#searchKey');
const searchButton = $('#search_button');
const searchResult = $('#result');
const moreResult = $('#moreResult');
let moreButton = $('<button>', {
    type: 'button',
    class: 'btn btn-light',
    text: 'More...'
});


$(document).ready(function () {
    searchButton.on('click', function (e) {
        e.preventDefault();
        defaultParams();
        if (params.keyword.length >= 3) {
            searchRequest();
        }
        else {
            alert('Недостаточно символов');
        }
    });
});
moreResult.on('click', function (e) {
    e.preventDefault();
    searchRequest();
});
searchKey.keypress(function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        searchButton.click();
    }
});
// Reset search
function defaultParams() {
    'use strict';
    variables.isTitle = false;
    variables.block_num = 0;
    params.page = 1;
    params.per_page = 5;
    params.keyword = searchKey.val().trim();
    params.source = 'database';
    variables.title_id = `title_${params.source}`;
    variables.found_id = `found_${params.source}`;
    searchResult.empty();
}

// send request
let searchRequest = function () {
    'use strict';
    $.ajax({
        url: '/api/find',
        method: 'post',
        dataType: 'json',
        data: params
    }).done(function (data) {
        doneSuccess(data);
    }).fail(function () {
    });
};

// handle data from server
let doneSuccess = function (data) {
    if (params.page === 1) {
        addInBody('result', [variables.title_id, variables.found_id, 'more']);
        setTitle(variables.title_id, totalFound(data.total));
    }
    handleData(data);
};

// handle data from server
function handleData(data) {
    if (data.total > 0) {
        if (data.status === 1) {
            foundBlock(data);
            moreResult.append(moreButton);
            params.page++;
        }
        else {
            moreResult.empty();
        }
    }
    else {
        setTitle(variables.title_id, 'No results found!');
    }
}

// Add found blocks to the page
let foundBlock = function (data) {
    $.each(data.items, function (key, value) {
        variables.block_num++;
        const html = `<div class="col-md-6 mb-3">
                    <div class="card cardl" title="${value.url} - ${value.descr}">
                        <a href="${value.url}" target="_blank">
                            <h5 class="card-header">${variables.block_num}. ${value.login} 
                                <span class="float-right"><i class="fa fa-${data.source}"></i></span></h5>
                            <div class="card-body">
                                <h5 class="card-title">${value.name}</h5>
                            </div>
                            <div class="card-footer text-muted">
                                <p class="card-text">
                                    <span class="pl-2"><i class="fa fa-star-o"></i> <strong>${value.stargazers}</strong></span>
                                    <span class="pl-2"><i class="fa fa-eye"></i> <strong>${value.watchers}</strong></span>
                                </p>
                            </div>
                        </a>
                    </div>
                </div>`;
        const card = $(html);
        $('#' + variables.found_id).append(card);
    });
};

// Set title text
let setTitle = function (title_id, text) {
    $(`#${title_id}`).html(`<h2 class="mb-3 mt-3">${text}</h2>`);
};
// Return formatted title text
let totalFound = function (total) {
    return (total > 0)
        ? 'Found: ' + formatNumber(total) + ' items'
        : 'No results found!';
};

// Format number
let formatNumber = (number) => number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");

// Add in body
function addInBody(container_id, idNames) {
    for (let idName of idNames) {
        appendToContainer(containerID, newDiv(idName, 'row'));
    }
}

// Create new div
function newDiv(id, className) {
    let div = document.createElement('div');
    div.className = className;
    div.id = id;
    return div;
}

// Append to container
function appendToContainer(container_id, blockElement) {
    let container = document.getElementById(container_id);
    container.appendChild(blockElement);
}






