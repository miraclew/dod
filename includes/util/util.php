<?php





/**
 * Reads/writes temporary data to cache files or session.
 *
 * @param  string $path File path within /tmp to save the file.
 * @param  mixed  $data The data to save to the temporary file.
 * @param  mixed  $expires A valid strtotime string when the data expires.
 * @param  string $target  The target of the cached data; either 'cache' or 'public'.
 * @return mixed  The contents of the temporary file.
 * @deprecated Please use Cache::write() instead
 */
/*
function cache($path, $data = null, $expires = '+1 day', $target = 'cache') {
    if (Configure::read('Cache.disable')) {
        return null;
    }
    $now = time();

    if (!is_numeric($expires)) {
        $expires = strtotime($expires, $now);
    }

    switch (strtolower($target)) {
        case 'cache':
            $filename = CACHE . $path;
        break;
        case 'public':
            $filename = WWW_ROOT . $path;
        break;
        case 'tmp':
            $filename = TMP . $path;
        break;
    }
    $timediff = $expires - $now;
    $filetime = false;

    if (file_exists($filename)) {
        $filetime = @filemtime($filename);
    }

    if ($data === null) {
        if (file_exists($filename) && $filetime !== false) {
            if ($filetime + $timediff < $now) {
                @unlink($filename);
            } else {
                $data = @file_get_contents($filename);
            }
        }
    } elseif (is_writable(dirname($filename))) {
        @file_put_contents($filename, $data);
    }
    return $data;
}
*/
/**
 * Used to delete files in the cache directories, or clear contents of cache directories
 *
 * @param mixed $params As String name to be searched for deletion, if name is a directory all files in
 *   directory will be deleted. If array, names to be searched for deletion. If clearCache() without params,
 *   all files in app/tmp/cache/views will be deleted
 * @param string $type Directory in tmp/cache defaults to view directory
 * @param string $ext The file extension you are deleting
 * @return true if files found and deleted false otherwise
 */
/*
function clearCache($params = null, $type = 'views', $ext = '.php') {
    if (is_string($params) || $params === null) {
        $params = preg_replace('/\/\//', '/', $params);
        $cache = CACHE . $type . DS . $params;

        if (is_file($cache . $ext)) {
            @unlink($cache . $ext);
            return true;
        } elseif (is_dir($cache)) {
            $files = glob($cache . '*');

            if ($files === false) {
                return false;
            }

            foreach ($files as $file) {
                if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
                    @unlink($file);
                }
            }
            return true;
        } else {
            $cache = array(
                CACHE . $type . DS . '*' . $params . $ext,
                CACHE . $type . DS . '*' . $params . '_*' . $ext
            );
            $files = array();
            while ($search = array_shift($cache)) {
                $results = glob($search);
                if ($results !== false) {
                    $files = array_merge($files, $results);
                }
            }
            if (empty($files)) {
                return false;
            }
            foreach ($files as $file) {
                if (is_file($file) && strrpos($file, DS . 'empty') !== strlen($file) - 6) {
                    @unlink($file);
                }
            }
            return true;
        }
    } elseif (is_array($params)) {
        foreach ($params as $file) {
            clearCache($file, $type, $ext);
        }
        return true;
    }
    return false;
}

*/
