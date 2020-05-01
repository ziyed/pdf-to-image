# Laravel library to convert a PDF to an Image

This Library helps to convert any PDF file to an Image 


## Dependencies installation

You should have [Imagick](http://php.net/manual/en/imagick.setresolution.php) and [Ghostscript](http://www.ghostscript.com/) installed.

- Install Ghostscript
```php
sudo apt-get install -y ghostscript

composer require org_heigl/ghostscript
```
See [issues regarding Ghostscript](#issues-regarding-ghostscript).

- Install ImageMagick 

```php
sudo apt-get install php-common gcc

sudo apt-get install imagemagick

sudo apt-get install php-imagick
```

See [issues regarding ImageMagick](#issues-regarding-imagemagick).

## Setup

- Make a directory name like `Libraries` under the Laravel `App` folder 
- Now Copy & Paste `Libraries/PdfToImage.php` to Laravel `App/Libraries/` folder


## Usage

Converting a pdf to an image is easy.

```php
$pdf = new App\Libraries\PdfToImage($pathToPdf);
$pdf->saveImage($pathToWhereImageShouldBeStored);
```

Converting a multi pages pdf to multiple images

```php
$path = storage_path() . "/app/public/documents/pdf_name.pdf";

$savePath = storage_path() . "/app/public/documents/pages/";

$pdf = new App\Libraries\PdfToImage($path);

foreach (range(1, $pdf->getNumberOfPages()) as $pageNumber) {
    $pageName = 'page_no'.'_'.$pageNumber;
    $pdf->setPage($pageNumber)->saveImage($savePath.$pageName);
}

```

If the path you pass to `saveImage` has the extensions `jpg`, `jpeg`, or `png` the image will be saved in that format.
Otherwise the output will be a `jpeg`.

## Other methods

You can get the total number of pages in the pdf:
```php
$pdf->getNumberOfPages(); //returns an int
```

By default the first page of the pdf will be rendered. If you want to render another page you can do so:
```php
$pdf->setPage(2)
    ->saveImage($pathToWhereImageShouldBeStored); //saves the second page
```

You can override the output format:
```php
$pdf->setOutputFormat('png')
    ->saveImage($pathToWhereImageShouldBeStored); //the output wil be a png, no matter what
```

You can set the quality of compression from 0 to 100:
```php
$pdf->setCompressionQuality(100); // sets the compression quality to maximum
```

## Issues regarding ImageMagick

If an error occur due to ImageMagick file reading from source like below

```
not authorized
```

This can be fixed by updating the ImageMagick policies. To do so, Open the policy file from `/etc/ImageMagick-6/policy.xml` and add the below lines of PDF permission to read|write


```
<policy domain="coder" rights="read|write" pattern="PDF,PS" />
```

Restart Apache server if running on it

```
sudo systemctl restart apache2
```

## Issues regarding Ghostscript

This package uses Ghostscript through Imagick. For this to work Ghostscripts `gs` command should be accessible from the PHP process. For the PHP CLI process (e.g. Laravel's asynchronous jobs, commands, etc...) this is usually already the case. 

However for PHP on FPM (e.g. when running this package "in the browser") you might run into the following problem:

```
Uncaught ImagickException: FailedToExecuteCommand 'gs'
```

This can be fixed by adding the following line at the end of your `php-fpm.conf` file and restarting PHP FPM. If you're unsure where the `php-fpm.conf` file is located you can check `phpinfo()`. If you are using Laravel Valet the `php-fpm.conf` file will be located in the `/usr/local/etc/php/YOUR-PHP-VERSION` directory.

```
env[PATH] = /usr/local/bin:/usr/bin:/bin
```

This will instruct PHP FPM to look for the `gs` binary in the right places.


## Inspired and Special Thanks To
-  [spatie/pdf-to-image](https://github.com/spatie/pdf-to-image)