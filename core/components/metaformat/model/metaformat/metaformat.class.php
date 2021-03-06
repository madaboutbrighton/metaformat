<?php

/**
 * MetaFormat
 *
 * Copyright 2016 by Mad About Brighton <mail@madaboutbrighton.net>
 * 
 * MetaFormat is free software; you can copy, distribute, transmit and adapt it
 * under the terms of the Creative Commons attribution-ShareAlike 3.0 Unported License.
 * 
 * You must attribute MetaFormat to Mad About Brighton. If you alter, transform, or build upon
 * MetaFormat, you must distribute the resulting work only under the same or similar license to this one.
 *
 * MetaFormat is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the Creative Commons attribution-ShareAlike 3.0 Unported License for more details.
 *
 * You should have received a copy of the license. If not, it can be viewed by visiting 
 * http://madaboutbrighton.net/projects/metaformat
 *
 */

/**
 * This file is the main class file for MetaFormat
 *
 * @copyright Copyright 2016 by Mad About Brighton <mail@madaboutbrighton.net>
 * @author west <west@madaboutbrighton.net>
 * @licence https://creativecommons.org/licenses/by-sa/3.0/
 * @package metaformat
 */

class MetaFormat
{
  /** @var modX|null A reference to the modX object */
  private $modx = null;

  /** @var modResource|null A reference to the modX resource */
	private $r = null;
  
  /** @var array A collection of properties to adjust MetaFormat behaviour */
  private $config = array();
  
  /** @var array A collection of placeholders generated by MetaFormat */
  private $placeholders = array();
  
  /** @var boolean|false Whether debugging is turned on */
  private $debug = false;
  
  /** @var string Relative path to the MetaFormat cache in the modX cacheManager */
  private $cache = 'web/metaformat/';
  
  /** @var string|null Encoded string that is used as a key for the modX cacheManager */
  private $cacheKey = null;
  
  /**
  * The MetaFormat Constructor.
  *
  * Creates a new MetaFormat object.
  *
  * @param modX &$modx A reference to the modX object.
  * @param array $config A collection of properties that modify MetaFormat behaviour.
  * @return MetaFormat A unique MetaFormat instance.
  */
  public function __construct(modX &$modx, array $config = array())
  {
    $this->modx =& $modx;
    
    // allows you to set paths in different environments
    $basePath = $this->modx->getOption('metaformat.core_path', $config, $this->modx->getOption('core_path').'components/metaformat/');
    $assetsUrl = $this->modx->getOption('metaformat.assets_url', $config, $this->modx->getOption('assets_url').'components/metaformat/');
    
    $this->config = array_merge(array(
        'basePath' => $basePath,
        'corePath' => $basePath,
        'modelPath' => $basePath.'model/',
        'processorsPath' => $basePath.'processors/',
        'templatesPath' => $basePath.'templates/',
        'chunksPath' => $basePath.'elements/chunks/',
        'jsUrl' => $assetsUrl.'js/',
        'cssUrl' => $assetsUrl.'css/',
        'assetsUrl' => $assetsUrl,
        'connectorUrl' => $assetsUrl.'connector.php',
        ),$config);
  }
  
  /**
  * Public entry point to generate meta tags
  *
  * @param array $param A collection of properties that modify MetaFormat behaviour.
  * @return string A set of follow-me icon links
  */
  public function getMetaFormat($param)
  { 
    $this->setup($param);
    
    if ($s = $this->cacheRead()) return $s;
    
    $this->r = $this->modx->getObject('modResource', $this->config['id']);
        
    $this->config['id'] = $this->r->get('id');

    $a = array();
    
    $a = array_merge($a, $this->appendChunks('preMain'));
   
    $a[] = $this->getChunk('MetaFormatMetaCharset', array('charset' => $this->config['charset']));
    
		$a = array_merge($a, $this->appendMetaEquiv());
		$a = array_merge($a, $this->appendMetaName());
		$a = array_merge($a, $this->appendPageTitle());
		$a = array_merge($a, $this->appendBase());
		$a = array_merge($a, $this->appendCanonical());
		$a = array_merge($a, $this->appendFiles('rss', array('media' => 'all', 'rel' => 'alternate', 'type' => $this->config['rssType']), 'MetaFormatLink'));
		$a = array_merge($a, $this->appendChunks('preCSS'));
		$a = array_merge($a, $this->appendFiles('css', array('media' => 'all', 'rel' => 'stylesheet', 'type' => 'text/css'), 'MetaFormatLink', $this->config['hostMedia']));
		$a = array_merge($a, $this->appendChunks('postCSS'));
		$a = array_merge($a, $this->appendChunks('preJS'));
    $a = array_merge($a, $this->appendFiles('js', array('type' => 'text/javascript'), 'MetaFormatScript', $this->config['hostCode']));
		$a = array_merge($a, $this->appendChunks('postJS'));
            
    $this->placeholders = array_merge(  $this->placeholders,
                                        $this->json2Placeholders( $this->config['socialItems'], 'metaformat.' ));
        
		$a = array_merge($a, $this->appendChunks('social', $this->placeholders));   
		$a = array_merge($a, $this->appendIcon());
    
    $a = array_merge($a, $this->appendChunks('postMain'));

    $s = implode("\n", $a);
    
    $this->cacheWrite($s);
    
    return $s;
  }

  /**
  * Adds some global default properties that are unique to each call to the main config properties
  *
  * @param array $param Existing properties
  */
  private function setup($param = array())
  {
    $this->debug = $this->getDebug($this->modx->getOption('debug', $param, false));
            
    $param['id'] = $this->modx->getOption('id', $param, $this->modx->resource->get('id'), true);
        
    $this->cacheKey = $this->getKey($param);    
    
    /* BUG - for some reason getOption turns UTF-8 into utf8 - will report */
    $param['charset'] = empty($param['charset']) ? $this->modx->getOption('modx_charset') : $param['charset'];
    
    if (!$this->modx->getOption('excludeBase', $param, false, true))
    {
      $param['base'] = $this->modx->getOption('base', $param, $this->modx->getOption('site_url'), true);
    }

    $this->config = array_merge($this->config, $param);
    
    //$this->log( $this->dumpArray( $this->config ) );
    
		$this->stringToArray( array('css', 'js', 'rss', 'forceNoIndex', 'preCSS', 'preJS', 'postCSS', 'postJS', 'social') );
  }
  
  /**
  * Convert json data into array of modx placeholders
  *
  * @param array $json JSON data to be converted
  * @param array $prefix Placeholder prefix
  * @return array Modx placeholders
  */  
  private function json2Placeholders($json, $prefix)
  {
    $a = json_decode($json, true);
    $b = array();
    
    if (is_array($a))
    {
      foreach ($a as $k => $v)
      {      
        $b[ $prefix . str_replace(':', '.', $k) ] = $v;
      }
    }
    
    return $b;
  }
  
  /**
  * Not in use yet
  */
  private function setDefualts($settings, $options, $defaults)
  {
    foreach ($defaults as $k => $v)
    {
      $this->config[$k] = $this->modx->getOption($settings[$k], $options, $v);
    }
  }
    
  /**
  * Generates the tags for a favicon or returns a processed chunk containing icon tags
  *
  * @return array An array of tags related to HEAD icon/favicon
  */
  private function appendIcon()
  {
    $a = array();

    $url = $this->config['favicon'];
    
		if (!empty($url))
		{
        $prefix = (empty($this->config['hostAll'])) ? $this->config['hostMedia'] : $this->config['hostAll'];

        $url = ($this->urlIsAbsolute($url)) ? $url : $prefix . $url;
        
        $a[] = $this->getChunk( 'MetaFormatLink', array('url' => $url, 'media' => 'all', 'rel' => 'shortcut icon', 'type' => 'image/x-icon') );
          
      } elseif (!empty($this->config['icon'])) {
        
        $a[] = $this->getChunk( $this->config['icon'] );
		}
    
    return $a;
  }
  
  /**
  * Iterates through an array of chunk names
  *
  * @param string $key Key of $this->config item
  * @param array $param Chunk properties
  * @return array An array of chunk outputs
  */
	private function appendChunks($key, $param = array())
	{
    $a = array();
        
    if (is_array($this->config[$key]))
    {
			foreach ($this->config[$key] as $v)
			{
				$v = trim($v);
        
        //$a[] = preg_replace('"(\r?\n){2,}"', "\n", trim($this->getChunk( $v, $param)));
      
        $a[] = trim($this->getChunk( $v, $param));
      }
    }
    
    return $a;
	}
  
  /**
  * Iterates through an array of file names
  *
  * @param string $key Key of $this->config item
  * @param array $fixed Chunk properties to be applied to each tag/chunk
  * @param string $chunk Name of chunk
  * @param string $host Name of host to append URL with
  * @return array An array of tags
  */
	private function appendFiles($key, $fixed, $chunk, $host = '')
	{
    $a = array();
    $prefix = '';
    
		if (is_array($this->config[$key]))
		{
      if (!empty($host))
      {
        $prefix = (empty($this->config['hostAll'])) ? $host : $this->config['hostAll'];
      }

			foreach ($this->config[$key] as $v)
			{
				$v = trim($v);

				if (is_numeric($v))
        { 
            //file to be generated from a resource id
            $scheme = ( empty($prefix) ) ? 'full' : -1;
                  
            $v = $prefix . $this->modx->makeUrl( $v, '', '', $scheme );
          
          } else {
            
            // file to be generated from a string url
            $v = ($this->urlIsAbsolute($v)) ? $v : $prefix . $v;
        }

				if (!empty($v))
				{
          $a[] = $this->getChunk( $chunk, array_merge( array('url' => $v), $fixed ) );
				}
			}
		}
    
    return $a;
	}
    
  /**
  * Checks if a URL is absolute
  *
  * @param string $url URL to be checked
  * @return boolean Whether the URL is absolute
  */
	private function urlIsAbsolute($url)
	{
    if (substr($url, 0, 2) == '//' || substr($url, 0, 4) == 'http' )  return true;
  }
  
  /**
  * Generates the TITLE tag for a resource
  *
  * @return array An array of TITLE tags
  */
	private function appendPageTitle()
	{
    $a = array();
    $b = array();

    $item = $this->config['titleItem'];

    if (!empty($item))
    {
      $id = $this->config['id'];

      for ( $i = 0; $i < $this->config['titleMax']; $i++ )
      {
        $resource = $this->modx->getObject('modResource', $id);
        
        //default to pagetitle if item returns empty content
        if (!$s = $this->getResourceItem($resource, $item )) $s = $this->getResourceItem($resource, 'pagetitle' );
        
        $this->appendArray($b, $s);
        
        $id = $resource->get('parent');

        if ( ! $id > 0) { break; }
      }

      $s = implode($this->config['titleSep'], $b);
            
      $a[] = $this->getChunk( 'MetaFormatTitle',  array('id' => $this->config['id'], 'content' => $s, 'titleSep' => $this->config['titleSep'], 'titleHomeSuffix' => $this->config['titleHomeSuffix']) );
    }
    
    return $a;
	}
  
  /**
  * Generates the canonical url tag for a resource
  *
  * @return array An array of LINK tags
  */
	private function appendCanonical()
	{
    $a = array();

    $url = $this->modx->makeUrl( $this->config['id'], '', '', 'full' );

    $a[] = $this->getChunk('MetaFormatLink', array('rel' => 'canonical', 'url' => $url));

    $this->placeholders['metaformat.canonical'] = $url;
    
		return $a;
	}
  
  /**
  * Generates the base url tag for a resource
  *
  * @return array An array of BASE tags
  */
	private function appendBase()
	{
    $a = array();
    
    if (!empty($this->config['base']))
    {
      $a[] = $this->getChunk( 'MetaFormatBase',  array('url' => $this->config['base']) );
    }
    
		return $a;
	}
  
  /**
  * Generates the Meta-Equiv tags for a resource
  *   HTML5 has depreciated this, but in some cases a legacy mode tag needs to use this
  *
  * @return array An array of META tags
  */
	private function appendMetaEquiv()
	{
    $a = array();
    
    if (!empty($this->config['legacyMode']))
    {
      $a[] = $this->getChunk( 'MetaFormatMetaEquiv',  array('name' => 'X-UA-Compatible', 'content' => $this->config['legacyMode']) );
    }
    
		return $a;
	}
  
  /**
  * Generates the standard META tags for a resource
  *
  * @return array An array of META tags
  */
	private function appendMetaName()
	{
    $a = array();
    
    if (!empty($this->config['viewport'])) $a[] = $this->getChunk( 'MetaFormatMetaName',  array('name' => 'viewport', 'content' => $this->config['viewport']) );

    if ($s = $this->getRobots()) $a[] = $this->getChunk( 'MetaFormatMetaName',  array('name' => 'robots', 'content' => $s) );
        
    if ($s = $this->getKeywords()) $a[] = $this->getChunk( 'MetaFormatMetaName',  array('name' => 'keywords', 'content' => $s) );
    
		if ($s = $this->getDescription()) $a[] = $this->getChunk( 'MetaFormatMetaName',  array('name' => 'description', 'content' => $s) );
      
    if ($s = $this->getAuthor()) $a[] = $this->getChunk( 'MetaFormatMetaName',  array('name' => 'author', 'content' => $s) );
      
		return $a;
	}

  /**
  * Returns the description for a resource
  *
  * @return string The description
  */
	private function getDescription()
	{
		if (!empty($this->config['descriptionItem']))
		{
      return $this->truncateToWholeWord($this->getResourceItem($this->r, $this->config['descriptionItem']), $this->config['descriptionMaxLength']);
		}
	}

  /**
  * Returns the author name for a resource, and makes some of the user details available for later
  *
  * @return string The authors  full name
  */
	private function getAuthor()
	{
		if (!empty($this->config['authorItem']))
		{
			$id = $this->r->get($this->config['authorItem']);

			if ($id > 0)
			{
				$user = $this->modx->getObject('modUserProfile', $id);

        $this->placeholders['metaformat.author'] = $user->get('fullname');
        $this->placeholders['metaformat.twitter.creator'] = $user->get('twitter');
        
				return $user->get('fullname'); 
			}
		}
	}

  /**
  * Returns the robot string, based on whether the site is live and whether the resource is searchable
  *
  * @return string The robot string
  */
	private function getRobots()
	{
		if ($this->config['siteLive'])
		{
				if (is_array($this->config['forceNoIndex']))
				{
					if (in_array($this->config['id'], $this->config['forceNoIndex']))
					{
						return $this->config['robotsNoIndex'];
					}
				} 
				
				return ($this->r->get('searchable')) ? $this->config['robotsIndex'] : $this->config['robotsNoIndex'];
				
			} else {
				        
				return $this->config['robotsNoIndex'];
		}
	}
  
  /**
  * Returns the keywords for a particular resource
  *
  * @return string Lower case, comma delimited string of keywords
  */
	private function getKeywords()
	{
		if (!empty($this->config['keywordsItem']))
		{
      $this->appendArray($a, $this->modx->getOption('site_name'));
      $this->appendArray($a, $this->modx->getOption('siteKeyWords')); 
      $this->appendArray($a, $this->getResourceItem($this->r, $this->config['keywordsItem']));
      $this->appendArray($a, $this->config['keywords']);

      if (is_array($a)) 
      {
        $s = implode(',', $a);
        
        $a = explode(',', $s);
        
        foreach ($a as $v)
        {
          $b[] = trim($v);
        }
        
        unset($a);
        
        return strtolower(implode(', ', array_unique($b)));
      }
    }
	}
  
  /**
  * Truncates a string without splitting it within a word
  *
  * @param string $string String to be truncated
  * @param string $desiredLength Maximum length of returned string
  * @return string The truncated string
  */
  private function truncateToWholeWord($string, $desiredLength)
  {
    $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
    $parts_count = count($parts);

    $length = 0;
    $last_part = 0;
    
    for (; $last_part < $parts_count; ++$last_part)
    {
      $length += strlen($parts[$last_part]);
      if ($length > $desiredLength) { break; }
    }

    return implode(array_slice($parts, 0, $last_part));
  }
  
  /**
  * Iterates through an array of $this->config items and turns them into arrays
  *
  * @param array $a Array of items
  */
	private function stringToArray($a)
	{
		foreach ($a as $v)
		{      
			$this->config[$v] = trim($this->modx->getOption($v, $this->config, ''));
           
      if (!empty($this->config[$v]))
      {          
        $this->config[$v] = explode(',', $this->config[$v]);
        
        if (is_array($this->config[$v]))
        {
          foreach ($this->config[$v] as &$value) { $value = trim($value); }
        }
      }
		}
	}
  
  /**
  * Appends a new value onto an array after escaping it
  *
  * @param array $a Array of items
  * @param string $v Value to be appended to array
  */
	private function appendArray(&$a, $v)
	{
		if (!empty($v)) { $a[] = htmlentities(trim($v)); }
	}
  
  /**
  * Returns the value of a resource field or template variable
  *
  * @param string $name Name of resource field or template variable
  * @param string $name Name of resource field or template variable
  * @return modResource A reference to the modX resource to interogate
  */
	private function getResourceItem(&$r, $name)
	{
		$a = explode('.', $name);

		if ($a[0] == 'tv')
		{
        return $r->getTVValue($a[1]);
      
/* 				if ($tv = $this->modx->getObject('modTemplateVar',array('name' => $a[1])))
				{
					return $tv->getValue( $this->config['id'] );
				}
 */				
			} else {

				return $r->get($name);
		}
	}
  
  /********** HELPER CLASSES **********/
     
  /**
  * Returns a dump of an array, for debugging purposes.
  *
  * @param array $a An array to be dumped
  * @param string $append String to be appended to each row
  * @return string The array transformed into a string
  */
  private function dumpArray($a, $append = "\n")
  {
    if (is_array($a))
    {
      foreach ($a as $k => $v)
      {
        $s.= $k . ' = ' . $v . $append;
      }
      
      return $s . $append . $append;
    }
  }
  
  /**
  * Transforms an array into an encoded string that is used as a key for the modX cacheManager
  *
  * @param array $a Any array to be transformed into a key
  * @return string A sha1 encoded string
  */
  private function getKey($param)
  {
    if (is_array($param))
    {
        $s = implode('', $param);

      } else {

        $s = get_class($this);
    }

    return sha1($s);
  }

  /**
  * Writes a string to the modX cacheManager
  *
  * @param string $s The string to be stored
  */
  private function cacheWrite($s)
  {
    $this->modx->cacheManager->set($this->cache . $this->cacheKey, $s);
  }

  /**
  * Reads a string from the modX cacheManager
  *
  * @return string The string that was stored against the key
  */
  private function cacheRead()
  {
    $s = $this->modx->cacheManager->get($this->cache . $this->cacheKey);

    if (!empty($s)) { return $s; }
  }
  
  /**
  * Calculate whether debug reporting should be on or off
  *
  * @param mixed $level The debug level passed in
  * @return boolean Whether debug reporting should be on or off
  */
  private function getDebug($level = false)
  {
    return ($level > 0 || $this->modx->getOption('debug') > 0);
  }
  
  /**
  * Log an entry if debug reporting is on
  *
  * @param string $s The debug message
  * @param integer $level The debug level modX::[LOG_LEVEL_FATAL, LOG_LEVEL_ERROR, LOG_LEVEL_WARN, LOG_LEVEL_INFO, LOG_LEVEL_DEBUG]
  * @return boolean Whether debug reporting should be on or off
  */
  private function log($s, $level = modX::LOG_LEVEL_ERROR)
  {
    if ($this->debug)
    {
      $this->modx->log($level, '[' . get_class($this) . '] ' . $s);
    }
  }

  /**
  * Processes a chunk. Attempts object first, then file based if not found
  *
  * @param string $name The name of the chunk
  * @param array $properties The settings for the chunk
  * @return string The content of the processed chunk
  */
  public function getChunk($name, $properties = array())
  {
      $chunk = null;
      
      if (!isset($this->chunks[$name]))
      {
          $chunk = $this->modx->getObject('modChunk', array('name' => $name));
          
          if (empty($chunk) || !is_object($chunk))
          {
            $chunk = $this->_getTplChunk($name);
            if ($chunk == false) return false;
          }
          
          $this->chunks[$name] = $chunk->getContent();
            
        } else {
          
          $o = $this->chunks[$name];
          $chunk = $this->modx->newObject('modChunk');
          $chunk->setContent($o);
      }
      
      $chunk->setCacheable(false);
      
      return $chunk->process($properties);
  }
  
  /**
  * Get the contents of a file based chunk
  *
  * @param string $name The name of the chunk
  * @param string $postfix The extension of the file based chunk
  * @return string The content of the file based chunk
  */
  private function _getTplChunk($name)
  {
      $chunk = false;
      
      $f = $this->config['chunksPath'] . 'chunk.' . strtolower($name) . '.tpl';
      
      if (file_exists($f))
      {
        $o = file_get_contents($f);
        $chunk = $this->modx->newObject('modChunk');
        $chunk->set('name',$name);
        $chunk->setContent($o);
      }
      
      return $chunk;
  }    
}

?>