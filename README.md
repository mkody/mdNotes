# mdNotes
Some stupid simple way to share rendred Markdown documents

The extension choosen is `.md`. You can read more about the syntax here:
http://daringfireball.net/projects/markdown/syntax

The files are stored inside the `data/` folder.

For content that you want to embed (like images) I'd recommend to create a 
separate folder (I use `host/`) next to the index.php file.  
It can then be used that way: `![Embedded picture](/host/pic.jpg)`

## How to add and open a file
For this example we'll use `doc.md` as the file
and `https://n.kdy.ch` as the base URL.

- Upload your `.md` file inside the `data/` folder.
- Go to the URL where the index.php is, i.e. `https://n.kdy.ch/`,
  and append `?f=doc` to the URL. The .md extension is optional.  
  The URL should now look like `https://n.kdy.ch/?f=doc`.
- You should be able to now see your file.

URL rewriting is available too, but I've inclued it only if you're using it at
the root of your (sub-)domain.

## Nice URLs
By default, you'll need to share URLs like `https://n.kdy.ch/?f=example` or 
`https://n.kdy.ch/index.php?f=example`.  
With some URL rewriting we can make it prettier, like `https://n.kdy.ch/example`

### Nginx Config
You'll need to add or edit the `location / {}` inside your `server {}` block,
along with your other properties (listen, server_name, root, index, php, ...):

```nginx
location / {
    # Check if a file exists, or route it to index.php
    try_files $uri $uri/ /index.php?f=$uri;
}
```

### Apache2
Your vHost should have `AllowOverride All` inside his
`<Directory /path/to/folder/>` node.

You should enable mod_rewrite with `a2enmode rewrite` and restart Apache2, too.

Then, the .htaccess distributed here sould work.
