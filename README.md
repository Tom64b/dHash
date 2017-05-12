dHash.php
=========

> A perceptual hash is a fingerprint of a multimedia file derived from various features from its content. Unlike cryptographic hash functions which rely on the avalanche effect of small changes in input leading to drastic changes in the output, perceptual hashes are "close" to one another if the features are similar.

This code was based on:
 - http://www.hackerfactor.com/blog/?/archives/529-Kind-of-Like-That.html
 - https://github.com/jenssegers/imagehash/

Installation
------------

Just put dhash.php where you want it.

Supported systems
-----------------

Most likely all (Windows/Linux/OSX, in 32bit and 64bit variants, are fine) 

Usage
-----

Calculating a perceptual hash for an image using the default implementation:

```php
include 'dhash.php';

$hash = dhash('path/to/image.jpg');
```

The resulting hash is a 64 bit hexadecimal image fingerprint that can be stored in your database once calculated. The hamming distance is used to compare two image fingerprints for similarities. Low distance values will indicate that the images are similar or the same, high distance values indicate that the images are different. Use the following method to detect if images are similar or not:

```php
$distance = dhash_distance($hash1, $hash2);
```

Equal images will not always have a distance of 0, so you will need to decide at which distance you will evaluate images as equal. In my tests distance of 5 means images are almost identical. But this will depend on the images and their number. For example; when comparing a small set of images, a lower maximum distances should be acceptable as the chances of false positives are quite low. If however you are comparing a large amount of images, 5 might already be too much.
If you want to check if a given high resolution image is the same as some thumbnail you should try a distance of 0.

Demo
----

These images are similar:

![Equals1](https://raw.githubusercontent.com/Tom64b/dHash/master/images/forest-high.jpg)
![Equals2](https://raw.githubusercontent.com/Tom64b/dHash/master/images/forest-copyright.jpg)

	Image 1 hash: e0f8fef6f2ecfcf4 (1110000011111000111111101111011011110010111011001111110011110100)
	Image 2 hash: e0f8eed4d2ecfcf4 (1110000011111000111011101101010011010010111011001111110011110100)
	Hamming distance: 4

These images are different:

![Equals1](https://raw.githubusercontent.com/Tom64b/dHash/master/images/tumblr_ndyfnr7lk21tubinno1_1280.jpg)
![Equals2]https://raw.githubusercontent.com/Tom64b/dHash/master/images/tumblr_ndyfq386o41tubinno1_1280.jpg)

	Image 1 hash: 68484849535b7575 (0110100001001000010010000100100101010011010110110111010101110101)
	Image 2 hash: e1c1e2a7bbaf6faf (1110000111000001111000101010011110111011101011110110111110101111)
	Hamming distance: 33


Differences from ImageHash by Jenssegers
----------------------------------------

There are some similarites to the code written by Jenssegers (and this readme is heavily inspired). 
So if you need a library for PHP and are wondering which one to choose, here's a few differences:

- dHash works on PHP 32 bit and 64 bit but always returns 64 bit hashes as hex strings while ImageHash return 64 bit hashes on 64 bit PHP and 32 bit hashes on 32 bit PHP
- dHash is just 50 lines of code, single file
- dHash implements only 1 hashing algorithm (difference hash) while ImageHash implements 2 more (perceptual hash and average hash)
- dHash has a few optimizations to make it faster especially when reading JPEG files. In some cases it's over 10 times faster
- while calculated distances are similar the actual hashes are different you can't compare hashes from dHash with those from ImageHash. This is due to a different implementation of the algorithm