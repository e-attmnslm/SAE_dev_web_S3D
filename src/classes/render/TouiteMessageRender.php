<?php
declare(strict_types=1);

namespace iutnc\touiteur\render;

class TouiteMessageRender {
    public static function render(
        string $message,
        bool $linkHashtags = false,
        ?string $imagePath = null
    ): string {
        $length = mb_strlen($message, 'UTF-8');
        $excerptLength = max(1, (int) ceil($length / 4));
        $excerpt = mb_substr($message, 0, $excerptLength, 'UTF-8');
        $formattedMessage = self::format($message, $linkHashtags);
        $hasImage = $imagePath !== null && $imagePath !== '';

        if ($length <= $excerptLength && !$hasImage) {
            return '<span class="touite-short">' . $formattedMessage . '</span>';
        }

        $image = !$hasImage
            ? ''
            : '<img src="' . htmlspecialchars($imagePath, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                . '" style="max-height: 200px; max-width: 200px;" alt="">';

        return sprintf(
            '<details class="touite-message"><summary>'
                . '<span class="touite-short">%s%s</span><span class="touite-full">%s</span>'
                . '</summary>%s</details>',
            self::format($excerpt, $linkHashtags),
            $length > $excerptLength ? '…' : '',
            $formattedMessage,
            $image
        );
    }

    private static function format(string $message, bool $linkHashtags): string {
        $message = htmlspecialchars_decode($message, ENT_QUOTES | ENT_HTML5);
        $message = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

       
        return $message;
      

       
    }
}
