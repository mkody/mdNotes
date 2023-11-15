# mdNotes
Some stupid simple way to share rendred Markdown documents

The extension choosen is `.md`. You can read more about the syntax here:  
http://daringfireball.net/projects/markdown/syntax

The files are stored inside the `data/` folder.

For content that you want to embed (like images), put them in the `data/` folder
and reference them like that: `![Embedded picture](/data/pic.jpg)`

Please note that this documentation focuses on installations where mdNotes is
used at the root of the domain.  
Do your research to use in a subfolder and adapt the `/assets` paths.


## Installation
- You'll need to have a working PHP environment and web server.  
  Please use at least PHP 8.0.
- Get a copy of this project:  
  Either with `git clone https://git.rita.moe/kody/mdNotes.git`  
  or [download a ZIP archive](https://git.rita.moe/kody/mdNotes/archive/master.zip).
- Configure your web server to point to the mdNotes folder.


## URL Rewriting
By default, you have to use URLs like `https://n.kdy.ch/?f=example`  
or `https://n.kdy.ch/index.php?f=example`.  
With some rewriting we can make it prettier, like `https://n.kdy.ch/example`

### Nginx Config
Add or edit the `location / {}` inside your `server {}` block,
next to with your other properties (listen, server_name, root, index, php, ...)
to include this `try_files`:

```nginx
location / {
    # Try to load as a file, a directory, or route it to index.php
    try_files $uri $uri/ /index.php;
}
```

### Apache2
Your vHost should have `AllowOverride All` inside its
`<Directory /path/to/folder/>` node.

Enable mod_rewrite with `a2enmode rewrite` and restart Apache2.

Then, the [`.htaccess`](./.htaccess) distributed here should work.


## How to add and open a file
For this example we'll use `doc.md` as the file
and `https://n.kdy.ch` as the base URL.

- Upload your `.md` file inside the `data/` folder.
- Type the base URL where the index.php is, i.e. `https://n.kdy.ch/`,
  and append `?f=doc` to it. The .md extension is optional.  
  The URL should now looks like `https://n.kdy.ch/?f=doc`.  
  If you have [URL rewriting](#url-rewriting) set, `https://n.kdy.ch/doc` should work!
