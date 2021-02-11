<style>
body {
    font-family: Arial, Helvetica, sans-serif;
}

nav>ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
    border: 1px solid #e7e7e7;
    background-color: #f2f2f2;
}

nav>ul li {
    display: inline-block;
}

nav>ul li a {
    display: block;
    color: #666;
    text-align: center;
    padding: .75rem 1rem;
    text-decoration: none;
}

nav>ul li a:hover:not(.active) {
    background-color: #ddd;
}

nav>ul li a.active {
    color: white;
    background-color: #fc292f;
}

table {
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    margin: .5rem 0;
}

table td,
table th {
    border: 1px solid #ddd;
    text-align: left;
    padding: .5rem;
}

table tr:nth-child(even) {
    background-color: #f2f2f2;
}

table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

input {
    margin: 0;
    display: inline-block;
    border: 1px solid #ccc;
    box-shadow: inset 0 1px 3px #ddd;
    border-radius: 3px;
    box-sizing: border-box;
    padding: .5rem;
}

input[type=button],
button,
a.button {
    margin: .25rem .15rem;
    display: inline-block;
    padding: .75rem 1rem;
    vertical-align: middle;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    background-color: inherit;
    text-align: center;
    cursor: pointer;
    white-space: nowrap;
    color: #FFFFFF;
    border: 0;
    background-color: #fc292f;
    box-shadow: none;
}

a.button.small {
    margin: 0;
    font-size: .75rem;
    padding: .25rem .5rem;
}

.notice {
    padding: .25rem .5rem;
}
</style>
