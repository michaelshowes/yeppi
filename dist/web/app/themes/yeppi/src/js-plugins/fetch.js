/**
 * Perform a fetch request of a URL that returns a Promise
 *
 * @param  {String} url - URL of the request
 * @param  {Object} options - Options to pass to fetch
 *
 * @return {Promise} response - Server response
 *
 * @docs https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch#Supplying_request_options
 *
 * @examples
 *
 * GET request:
 * fetch('https://jsonplaceholder.typicode.com/posts')
 *   .then(response => response.json())
 *   .then(data => {
 *     // where you want your code to execute
 *     // data will be your response data in JSON
 *     console.log(data);
 *   })
 * ;
 *
 * POST request:
 * fetch('https://jsonplaceholder.typicode.com/posts', {
 *   method: 'POST',
 *   body: JSON.stringify({
 *     title: 'foo',
 *     body: 'bar',
 *     userId: 1
 *   }),
 *   headers: {
 *     'Content-type': 'application/json'
 *   }
 * })
 *   .then(response => response.json())
 *   .then(json => console.log(json))
 * ;
 */
export default (url, options = null) => {

  return fetch(url, options)
    .then(response => {
      if (!response.ok) {
        throw Error(response.statusText);
      }
      return response;
    })
    /* eslint-disable no-console */
    .catch(error => console.error(error))
  ;
};
