<?php
namespace WormOfTime\Image;

use WormOfTime\Response\Response;

class Image
{
    use Response;

    protected $mimeTypeMap = array(
        IMAGETYPE_GIF => "gif",
        IMAGETYPE_JPEG => "jpg",
        IMAGETYPE_PNG => "png",
        IMAGETYPE_SWF => "swf",
        IMAGETYPE_PSD => "psd",
        IMAGETYPE_BMP => "bmp",
        IMAGETYPE_TIFF_II => "tiff",
        IMAGETYPE_TIFF_MM => "tiff",
        IMAGETYPE_JPC => "jpc",
        IMAGETYPE_JP2 => "jp2",
        IMAGETYPE_JPX => "jpx",
        IMAGETYPE_JB2 => "jb2",
        IMAGETYPE_SWC => "swc",
        IMAGETYPE_IFF => "iff",
        IMAGETYPE_WBMP => "wbmp",
        IMAGETYPE_XBM => "xbm",
        IMAGETYPE_ICO => "ico"
    );

    protected $image_type = 0;

    /**
     * @var mixed|resource
     */
    protected $bottom_image;

    public function __construct($bottom_image_url)
    {
        $image_info = $this->getImage($bottom_image_url);
        $this->image_type = $image_info['image_type'];
        $this->bottom_image = $image_info['im'];
    }

    /**
     * @return array
     * 检查是否有效的图片类型
     */
    public function checkImageType()
    {
        if ($this->getCode() > 0) {
            return $this->json();
        }

        return $this->success();
    }

    /**
     * @param $image_url
     * @param $new_image_url
     * @param int $max_width
     * @param int $max_height
     * @param bool $is_return_resource
     * @return bool|resource
     * 等比例缩放图片
     */
    public function reSize($image_url, $new_image_url, $max_width = 145, $max_height = 145, $is_return_resource = false)
    {
        $info = $this->getImage($image_url);
        $im = $info['im'];
        $width = $info['width'];
        $height = $info['height'];

        // 计算缩放比例
        $w_scale = ($max_width / $width);
        $h_scale = ($max_height / $height);
        if ( $w_scale > $h_scale ) {
            $scale = $h_scale;
        } else {
            $scale = $w_scale;
        }

        //计算出缩放后的尺寸
        $new_width = floor($width * $scale);
        $new_height = floor($height * $scale);

        //创建一个新的图像源(目标图像)
        $new_im = imagecreatetruecolor($new_width, $new_height);
        //执行等比缩放
        imagecopyresampled($new_im, $im, 0,0,0,0, $new_width, $new_height, $width, $height);
        imagedestroy($im);

        if ($is_return_resource) {
            return $new_im;
        }

        $this->saveImage($new_im, $new_image_url, $info['image_type']);
        imagedestroy($new_im);
        return true;
    }

    /**
     * @param string $image_url 图片地址
     * @param int $pos_x 图片的x轴坐标
     * @param int $pos_y 图片的y轴坐标
     * @param string $save_path 合成图片保存地址
     * @param bool $is_return_resource
     * @return bool|mixed|resource
     * 合成图片
     */
    public function mergeImage($image_url, $pos_x = 0, $pos_y = 0, $save_path = '', $is_return_resource = false)
    {
        $info = $this->getImage($image_url);
        $im = $info['im'];
        $width = $info['width'];
        $height = $info['height'];

        // 合成图片
        imagecopymerge($this->bottom_image, $im, $pos_x, $pos_y, 0, 0, $width, $height, 100);
        // 释放资源
        imagedestroy($im);

        if ($is_return_resource) {
            return $this->bottom_image;
        }

        $this->saveImage($this->bottom_image, $save_path, $this->image_type);
        return true;
    }

    /**
     * @param $image_url
     * @param string $save_path
     * @param bool $is_return_resource
     * @return bool|false|resource
     * 创建圆形图片
     */
    public function createCircleImage($image_url, $save_path = '', $is_return_resource = false)
    {
        $info = $this->getImage($image_url);
        $im = $info['im'];
        $width = $info['width'];
        $height = $info['height'];

        // 创建一张画布
        $new_im = imagecreatetruecolor($width, $height);
        // 填充透明颜色
        $bg = imagecolorallocatealpha($new_im, 255, 255, 255, 127);
        imagesavealpha($new_im, true);
        imagefill($new_im, 0, 0, $bg);

        $r = $width / 2;
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgbColor = imagecolorat($im, $x, $y);
                if (((($x-$r) * ($x-$r) + ($y-$r) * ($y-$r)) < ($r*$r))) {
                    imagesetpixel($new_im, $x, $y, $rgbColor);
                }
            }
        }

        imagedestroy($im);

        if ($is_return_resource) {
            return $new_im;
        }

        $this->saveImage($new_im, $save_path);
        imagedestroy($new_im);
        return true;
    }

    /**
     * 释放图片资源
     */
    public function destroyImage()
    {
        imagedestroy($this->bottom_image);
    }

    /**
     * @param resource $im
     * @param string $path
     * @param int $type
     * @return bool
     * 保存图片信息
     */
    private function saveImage($im, $path, $type = 0): bool
    {
        switch ($type) {
            case IMAGETYPE_GIF:
                imagegif($im, $path);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($im, $path);
                break;
            case IMAGETYPE_PNG:
                imagepng($im, $path);
                break;
            case IMAGETYPE_BMP:
                imagebmp($im, $path);
                break;
            case IMAGETYPE_WBMP:
                imagewbmp($im, $path);
                break;
            case IMAGETYPE_XBM:
                imagexbm($im, $path);
                break;
            default:
                $this->setCode(40031);
                $this->setMessage('错误的图片类型');
                return false;
        }

        return true;
    }

    /**
     * @param $image_url
     * @return array
     */
    private function getImage($image_url)
    {
        $size_info = getimagesize($image_url);

        $image_type = 0;
        $bottom_image = null;

        switch ($size_info['mime']) {
            case 'image/gif':
                $image_type = IMAGETYPE_GIF;
                $bottom_image = imagecreatefromgif($image_url);
                break;
            case 'image/jpeg':
                $image_type = IMAGETYPE_JPEG;
                $bottom_image = imagecreatefromjpeg($image_url);
                break;
            case 'image/png':
                $image_type = IMAGETYPE_PNG;
                $bottom_image = imagecreatefrompng($image_url);
                break;
            case 'image/bmp':
                $image_type = IMAGETYPE_BMP;
                $bottom_image = imagecreatefrombmp($image_url);
                break;
            case 'image/vnd.wap.wbmp':
                $image_type = IMAGETYPE_WBMP;
                $bottom_image = imagecreatefromwbmp($image_url);
                break;
            case 'image/xbm':
                $image_type = IMAGETYPE_XBM;
                $bottom_image = imagecreatefromxbm($image_url);
                break;
            default:
                $this->setCode(40031);
                $this->setMessage('错误的图片类型');
                break;
        }

        return [
            'width' => $size_info[0],
            'height' => $size_info[1],
            'image_type' => $image_type,
            'im' => $bottom_image
        ];
    }
}