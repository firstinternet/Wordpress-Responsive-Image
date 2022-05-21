<?php

namespace FirstInternet;

class ResponsiveImage
{
    private
    $imageId,
    $size,
    $sources,
    $sizes,
    $class,
    $alt,
    $attributes,
    $lazyLoad,
    $lazyClass,
    $webp,
    $style,
    $gridWidths = [
        'xxs' => [
            'media' => [
                'min' => false,
                'max' => 399.98
            ],
            'containerMax' => 400
        ],
        'xs' => [
            'media' => [
                'min' => false,
                'max' => 575.98
            ],
            'containerMax' => 575
        ],
        'sm' => [
            'media' => [
                'min' => 576,
                'max' => 767.98
            ],
            'containerMax' => 540
        ],
        'md' => [
            'media' => [
                'min' => 768,
                'max' => 991.98
            ],
            'containerMax' => 720
        ],
        'lg' => [
            'media' => [
                'min' => 992,
                'max' => 1199.98
            ],
            'containerMax' => 960
        ],
        'xl' => [
            'media' => [
                'min' => 1200,
                'max' => 1399.98
            ],
            'containerMax' => 1140
        ],
        'xxl' => [
            'media' => [
                'min' => 1400,
                'max' => false
            ],
            'containerMax' => 1320
        ]        
    ];

    public function __construct ( Int $imageId, String $size = 'full' )
    {
        $this->imageId = $imageId;
        $this->size = $size;
    }

    public function setSources( Array $sources )
    {
        $this->sources = $sources;
        return $this;
    }

    public function setStyle( string $style )
    {
        $this->style = $style;
        return $this;
    }

    public function lazyLoad( String $lazyClass = 'lazy' )
    {
        $this->lazyLoad = true;
        $this->lazyClass = $lazyClass;
        $this->class .= ' ' . $this->lazyClass;
        return $this;
    }

    public function webp()
    {
        $this->webp = true;
        return $this;
    }

    public function setClass( String $class )
    {
        $this->class .= ' ' . $class;
        return $this;
    }

    public function setAlt( String $alt )
    {
        $this->alt = $alt;
        return $this;
    }

    public function setAttributes( array $attributes )
    {
        foreach( $attributes as $attribute ) {
            if ( is_array($attribute) && count($attribute) > 1 ) {
                $attributeString = ' ' . $attribute[0] . '=';
                $attributeString .= '"' . $attribute[1] . '"';
                $this->attributes .= $attributeString;
            }
        }
        return $this;
    }

    public function setSizes( String $sizes )
    {
        $this->sizes = $sizes;
        return $this;
    }

    public function autoSizes( String $classes )
    {
        $splitClasses = explode(' ', $classes);
        $colSizes = [];

        foreach ( $splitClasses as $class ) {
            if ( strpos($class, 'col') !== false ) {
                $class = explode('-', $class);
                if ( count($class) == 1 && $class[0] == 'col' ) {
                    $colSizes['xs'] = 12;
                } elseif ( count($class) == 2 && ctype_digit($class[1]) ) {
                    $colSizes['xs'] = (int)$class[1];
                } else {
                    $colSizes[$class[1]] = (int)$class[2];
                }
            }
        }

        // Default to replicate Bootstrap's behaviour if 'col' or 'col-12' class isn't specified
        if ( !isset($colSizes['xs']) ) {
            $colSizes = [
                'xs' => 12
            ];
        }

        $breakpoints = ['xs', 'sm', 'md', 'lg', 'xl', 'xxl'];

        // Loop through all breakpoints and fill in gaps between specified col sizes
        foreach ( $breakpoints as $key => $size ) {
            if ( !isset($colSizes[$key]) ) {
                $prevKey = $key--;
                $prevSize = $breakpoints[$prevKey];
                while ( !isset($colSizes[$prevSize]) ) {
                    $prevKey--;
                    $prevSize = $breakpoints[$prevKey];
                }
                $colSizes[$size] = $colSizes[$prevSize];
            }
        }

        $colSizes = array_merge(array_flip($breakpoints), $colSizes);

        $sizes = '';

        // Construct sizes string covering all bootstrap breakpoints
        foreach ( $colSizes as $screenSize => $size ) {
            $media = $this->gridWidths[$screenSize]['media'];
            $containerMax = $this->gridWidths[$screenSize]['containerMax'];
            $maxImageWidth = round( ( ($containerMax / 12) * $size ) - 24 );

            if ( !$media['min'] && $media['max'] ) {
                $sizes .= '(max-width: 450px) calc(100vw - 24px), (min-width: 450px) and (max-width: ' . $media['max'] . 'px) ' . $maxImageWidth . 'px, ';
            } elseif ( $media['min'] && $media['max'] ) {
                $sizes .= '(min-width: ' . $media['min'] . 'px) and (max-width: ' . $media['max'] . 'px) ' . $maxImageWidth . 'px, ';
            } elseif ( $media['min'] && !$media['max'] ) {
                $sizes .= '(min-width: ' . $media['min'] . 'px) ' . $maxImageWidth . 'px';
            }
        }

        $this->sizes = $sizes;
        return $this;
    }

    private function replaceWebp( String $source )
    {
        return str_replace(
            ['.jpg', '.jpeg', '.png', '<source '],
            ['.jpg.webp', '.jpeg.webp', '.png.webp', '<source type="image/webp" '],
            $source
        );
    }

    private function createSources()
    {
        $sourcesString = '';

        if ( $this->sources ) {

            foreach ( $this->sources as $sourceSize => $sourceName ) {
                $srcset = esc_attr( wp_get_attachment_image_url( $this->imageId, $sourceName ) );

                if ( $sourceSize == 'xxs' ) {
                    $media = '(max-width: ' . $this->gridWidths['xxs']['media']['max'] . 'px)';
                } elseif ( $sourceSize == 'xs' ) {
                    $media = '(min-width: 400px) and (max-width: ' . $this->gridWidths['xs']['media']['max'] . 'px)';
                } else {
                    $minMax = $this->gridWidths[$sourceSize]['media'];
                    if ( !$minMax['max'] ) {
                        $media = '(min-width: ' . $minMax['min'] . 'px)';
                    } else {
                        $media = '(min-width: ' . $minMax['min'] . 'px) and (max-width: ' . $minMax['max'] . 'px)';
                    }
                }

                $sources[] = '<source srcset="' . $srcset . '" media="' . $media . '">';
            }

            if ( $this->webp ) {
                foreach ( $sources as $source ) {
                    $sourcesString .= $this->replaceWebp($source);
                }
            }

            // Generate fallback sources for browsers which don't support webp
            foreach ( $sources as $source ) {
                $sourcesString .= $source;
            }

        } else {

            $srcset = esc_attr(wp_get_attachment_image_srcset( $this->imageId, $this->size ));

            if ( $srcset ) {
                if ( $this->webp ) {
                    $sourcesString .= '<source type="image/webp" srcset="' . $this->replaceWebp($srcset) . '" sizes="' . $this->sizes . '">';
                }
                $sourcesString .= '<source srcset="' . $srcset . '" sizes="' . $this->sizes . '">';
            } else {
                // If the original image is small, WordPress doesn't generate a srcset. In that case, use src as the srcset.
                $src = esc_attr( wp_get_attachment_image_url( $this->imageId, $this->size ) );
                if ( $src ) {
                    if ( $this->webp ) {
                        $sourcesString .= '<source type="image/webp" srcset="' . $this->replaceWebp($src) . '">';
                    }
                    $sourcesString .= '<source srcset="' . $src . '">';
                }
            }


        }

        return $sourcesString;
    }

    private function constructPictureElement()
    {
        $imageArray = wp_get_attachment_image_src( $this->imageId, $this->size );

        if ( !$imageArray ) {
            return;
        }

        $src = esc_attr( $imageArray[0] );
        $intrinsicWidth = $imageArray[1];
        $intrinsicHeight = $imageArray[2];

        $alt = $this->alt ? $this->alt : get_post_meta( $this->imageId, '_wp_attachment_image_alt', true );
        $sources = $this->createSources();

        $img  = '<img src="' . $src . '"';
        $img .= ' alt="' . $alt . '"';
        $img .= ' width="' . $intrinsicWidth . '"';
        $img .= ' height="' . $intrinsicHeight . '"';

        if ( $this->class ) {
            $img .= ' class="' . trim($this->class) . '"';
        }

        if ( $this->style ) {
            $img .= ' style="' . $this->style . '"';
        }

        if ( $this->attributes ) {
            $img .= ' ' . $this->attributes;
        }

        $img .= '>';

        $picture  = '<picture>';
        $picture .= $sources;
        $picture .= $img;
        $picture .= '</picture>';

        if ( $this->lazyLoad ) {
            $fallbackImg = str_replace($this->lazyClass, '', $img);
            $fallbackImg = '<noscript>' . $fallbackImg . '</noscript>';

            $picture = str_replace(
                ['src="', 'srcset="', 'sizes="'],
                ['data-src="', 'data-srcset="', 'data-sizes="'],
                $picture
            );

            $picture .= $fallbackImg;
        }

        return $picture;
    }

    public function generate()
    {
        $picture = $this->constructPictureElement();
        echo $picture;
    }

    public function get()
    {
        $picture = $this->constructPictureElement();
        return $picture;
    }
}
