import classNames from "classnames";
import { useState } from "react";
import { useDebouncedCallback } from "use-debounce";
import SearchIcon from "../../assets/icons/SearchIcon";

import './SearchBar.scss';

export default function SearchBar({ autoFocus, onClickUser, ...props }) {
  const [text, setText] = useState('');
  const [resultsVisible, setResultsVisible] = useState(false);
  const [results, setResults] = useState([]);

  const queryText = useDebouncedCallback(async value => {
    const { data } = await axios.get(`/frontend/search?q=${encodeURI(value)}`);
    setResults(data);
  }, 200, { leading: true, maxWait: 500 });

  const toggleResults = value => {
    if (value.length < 4) {
      setResultsVisible(false);
    }
    else {
      queryText(value);
      setResultsVisible(true);
    }
  }

  const changeText = e => {
    const { value } = e.target;
    setText(value);
    toggleResults(value);
  }

  const focusSearch = () => {
    toggleResults(text);
  }

  // const blurSearch = () => {
  //   setResultsVisible(false);
  // }

  const cancelSearch = () => {
    setResultsVisible(false);
    setText('');
  }

  const loadUser = async username => {
    const { data } = await axios.post(`http://localhost:2121/frontend/search?q=${username}`);

    setResultsVisible(false);
    onClickUser(data.users);
  }

  return (
    <div className={classNames('__search-bar', { 'results-visible': resultsVisible })}>
      <SearchIcon className='__search-bar-icon' />
      <input type='text' autoFocus onFocus={focusSearch} /*onBlur={blurSearch}*/ spellCheck={false} value={text} onChange={changeText} placeholder={'Twitter handle...'} />
      { resultsVisible && <div className="__search-bar-close" role='button' onClick={cancelSearch}>×</div> }
      { resultsVisible && (
        <div className='__search-bar-results'>
          <div className='__search-bar-contents'>
            { results?.length === 0 && (
              <div className="__search-bar-empty">No users found.</div>
            )}
            { results.map(result => (
              <div key={result.twitter_username} className='__search-bar-item' onClick={() => loadUser(result.twitter_username)}>
                <div className='__search-bar-username'>{ result.name }</div>
                <div className="__search-bar-handle">@{ result.twitter_username }</div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
