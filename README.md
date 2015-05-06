# mdNotes
Just to share some documents formated in Markdown format publicly.

The extension choosen is `.md`. You can read more about the syntax here:
http://daringfireball.net/projects/markdown/syntax

The files are stored inside the `data/` folder. 
You can upload images inside and add them in your document.

## How to add and open a file
- Upload a .md file inside the `data/` folder. Let's say a `exemple.md`
- Go to the URL where the index.php is
- Use this URL and add `?f=<FILE>`. `<FILE>` should be the name of the file without the extension. Like `exemple`.

URL rewriting is available too, but I've inclued it only if you're using
it at the root of your (sub-)domain.

## Nice URLs
By default, you'll need to share URL like `http://n.kdy.ch/?f=exemple` or `http://n.kdy.ch/index.php?f=exemple` to open the exemple.md file.
With some URL Rewriting, you can have URLs like `http://n.kdy.ch/exemple`

### Nginx Config
You'll need to change or add this location bloc on your vHost, 
along with the rest (listen, server_name, root, index, php, ...)

```nginx
location / {
    # Check if a file exists, or route it to index.php.
    try_files $uri $uri/ /index.php?f=$uri;
}
```

### Apache2
Your vHost should have `AllowOverride All` inside his 
`<Directory /path/to/folder/>` bloc.

You should enable mod_rewrite with `a2enmode rewrite` and restart Apache2, too.

Then, the .htaccess distributed here sould work.
