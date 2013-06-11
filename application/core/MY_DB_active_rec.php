<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class MY_DB_active_record extends CI_DB_active_record
{
/**
 * variable to store the requested WHERE clause bracket state
 */
var $ar_bracket_open            = FALSE;
var $last_bracket_type        = 'where';

/**
 * Allows you to insert brackets into your WHERE clause
 *
 * @access    public
 * @param    string
 * @return    object
 */
function bracket($type = NULL)
{
    if ( strtolower($type) == 'open' )
    {
        $this->ar_bracket_open = TRUE;
    }
    elseif ( strtolower($type) == 'close' )
    {
        // fetch the key of the last entry added
        end($this->ar_where);
        $key = key($this->ar_where);

        // add a bracket close
        $this->ar_where[$key] .= ')';

        // update the AR cache clauses as well
        if ($this->ar_caching === TRUE)
        {
            $this->ar_cache_where[$key] = $this->ar_where[$key];
        }
    }

    return $this;
}

// --------------------------------------------------------------------

/**
 * Where
 *
 * Called by where() or orwhere()
 *
 * @access    private
 * @param    mixed
 * @param    mixed
 * @param    string
 * @return    object
 */
function _where($key, $value = NULL, $type = 'AND ', $escape = NULL)
{
    // store this as the last_bracket_type
    $this->last_bracket_type = 'where';

    // call the original method
    $result = parent::_where($key, $value, $type, $escape);

    // do we need to add a bracket open
    if ( $this->ar_bracket_open )
    {
        // fetch the key of the last entry added
        end($this->ar_where);
        $key = key($this->ar_where);

        // was this the first entry?
        if ( $key == 0 )
        {
            // first where clause, simply prefix it with a bracket open
            $this->ar_where[$key] = '(' . $this->ar_where[$key];
        }
        else
        {
            // subsequent where clause, strip the type before adding the bracket open
            $this->ar_where[$key] = $type . ' (' . substr($this->ar_where[$key], strlen($type));
        }

        // reset the bracket state
        $this->ar_bracket_open = FALSE;

        // update the AR cache clauses as well
        if ($this->ar_caching === TRUE)
        {
            $this->ar_cache_where[$key] = $this->ar_where[$key];
        }
    }

    // return the result
    return $result;
}

// --------------------------------------------------------------------

/**
 * Like
 *
 * Called by like() or orlike()
 *
 * @access    private
 * @param    mixed
 * @param    mixed
 * @param    string
 * @return    object
 */
function _like($field, $match = '', $type = 'AND ', $side = 'both', $not = '')
{
    // store this as the last_bracket_type
    $this->last_bracket_type = 'like';

    // do we already have entries for the where clause?
    if ( $where_count = count($this->ar_where) > 0 )
    {
        // yes, add a dummy entry to force the $type being added
        $this->ar_like[] = '';
    }

    // call the original method
    $result = parent::_like($field, $match, $type, $side, $not);

    // fetch the key of the last entry added
    end($this->ar_like);
    $key = key($this->ar_like);

    // do we need to add an open bracket
    if ( $this->ar_bracket_open  )
    {
        // was this the first entry?
        if ( $where_count == 0 )
        {
            // first where clause, simply prefix it with a bracket open
            $this->ar_like[$key] = '(' . $this->ar_like[$key];
        }
        else
        {
            // subsequent where clause, strip the type before adding the bracket open
            $this->ar_like[$key] = $type . ' (' . substr($this->ar_like[$key], strlen($type));
        }

        // reset the bracket state
        $this->ar_bracket_open = FALSE;

        if ($this->ar_caching === TRUE)
        {
            $this->ar_cache_like[$key] = $this->ar_like[$key];
        }
    }

    // add the like to the ar_where array to maintain where clause sequence
    $this->ar_where[] = $this->ar_like[$key];
    $this->ar_like = array();

    // return the result
    return $result;
} 
}
