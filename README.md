# First Internet Responsive Image Class

## Overview

The responsive image class was built by a team of developers at [First Internet](https://www.firstinternet.co.uk).

The `ResponsiveImage` class is a PHP utility designed to generate responsive images for WordPress. It supports multiple image sources, lazy loading, WebP format, and dynamic sizing based on Bootstrap-like breakpoints.

## Features

- **Responsive Image Handling**: Supports multiple image sizes for different breakpoints.
- **Lazy Loading**: Optionally enables lazy loading for performance optimization.
- **WebP Support**: Automatically replaces image formats with WebP versions if enabled.
- **Custom Attributes**: Allows adding custom attributes like classes, alt text, and inline styles.
- **Automatic Sizing**: Can determine image sizes dynamically based on Bootstrap column classes.
- **WordPress Integration**: Works with WordPress media library functions.

## Installation

Ensure that your WordPress theme or plugin includes this class and that you have the necessary WordPress functions available.

1. Copy the `ResponsiveImage.php` file into your theme or plugin.
2. Include it in your project:

```php
require_once 'path/to/ResponsiveImage.php';
```

## Usage

### Basic Example

```php
use FirstInternet\ResponsiveImage;

$image = new ResponsiveImage(123, 'medium');
$image->generate();
```

This outputs a responsive `<picture>` element for the image with ID `123`, using the `medium` size.

### Customizing Output

```php
$image = new ResponsiveImage(123, 'large');
$image->setClass('custom-class')
      ->setAlt('An example image')
      ->lazyLoad()
      ->webp()
      ->generate();
```

### Using `get()` Instead of `generate()`

If you need to store the image markup in a variable:

```php
$imageMarkup = (new ResponsiveImage(123, 'full'))->get();
echo $imageMarkup;
```

## Methods

### `__construct(int $imageId, string $size = 'full')`

- **$imageId** *(int)* - WordPress attachment ID.
- **$size** *(string, optional)* - Image size (default: `'full'`).

### `setSources(array $sources)`

Sets different image sources for various breakpoints.

```php
$image->setSources([
    'sm' => 'small-image',
    'md' => 'medium-image',
    'lg' => 'large-image'
]);
```

### `lazyLoad(string $lazyClass = 'lazy')`

Enables lazy loading by default with the `'lazy'` class.

```php
$image->lazyLoad('custom-lazy-class');
```

### `webp()`

Enables WebP format support.

```php
$image->webp();
```

### `setClass(string $class)`

Adds custom classes to the `<img>` element.

```php
$image->setClass('img-fluid rounded');
```

### `setAlt(string $alt)`

Sets the image alt text.

```php
$image->setAlt('Descriptive alt text');
```

### `setAttributes(array $attributes)`

Adds custom attributes to the `<img>` element.

```php
$image->setAttributes([
    ['data-example', 'value'],
    ['aria-hidden', 'true']
]);
```

### `setSizes(string $sizes)`

Sets the `sizes` attribute manually.

```php
$image->setSizes('(max-width: 600px) 100vw, 50vw');
```

### `autoSizes(string $classes)`

Automatically calculates image sizes based on Bootstrap grid classes.

```php
$image->autoSizes('col-md-6 col-lg-4');
```

### `generate()`

Outputs the `<picture>` element directly.

### `get()`

Returns the `<picture>` element as a string.

## Example Output

```html
<picture>
    <source srcset="example-small.jpg" media="(max-width: 575px)">
    <source srcset="example-medium.jpg" media="(min-width: 576px) and (max-width: 991px)">
    <source srcset="example-large.jpg" media="(min-width: 992px)">
    <img src="example-large.jpg" alt="Example Image" class="custom-class lazy" width="800" height="600">
</picture>
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

This class provides a flexible way to manage responsive images in WordPress, improving performance and user experience.

For more information, visit [First Internet](https://www.firstinternet.co.uk).

