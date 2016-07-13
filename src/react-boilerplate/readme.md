# What is react-boilerplate?
This is the folder where all development is initiated for React based components to be used within the Laravel framework folder. The current workflow is:

1. develop a component in react-boilerplate
2. run the build process within the folder to export all assets to Laravel.

## The architecture

### Prerequisites
* node cli installed on machine (version 6+)

### Setup
to get going after satisfying the prerequisites, navigate to `elastic-erga/react-boilerplate/` directory and run `npm i` from the command line. This will install all the dependencies for your version of node to `elastic-erga/react-boilerplate/node_modules`.

### Tech stack
React (UI)
React-Router (component mapping to url segment)
Redux (internal data management)

### Build & Helpers
Whilst developing the environment has been configured to support all ECMAScript 2015 features & SASS all preprocessed by Webpack. To add newer ECMAScript support run `npm i` + (name of babel plugin or preset), then add these references to the `.babelrc` file. please see [https://babeljs.io/docs/plugins/](https://babeljs.io/docs/plugins/) for more details.

### Component Development
Each component should have its own base folder that holds its own assets. The folder should be created in `elastic-erga/react-boilerplate/components`. Assets should include SASS & JS.


> Note: images have not yet been used on the project. Therefore implementation of images would require an additional config step added in the webpack config.


When requiring data for the component Redux has been included using the best practices from [http://redux.js.org/index.html](http://redux.js.org/index.html).

a breakdown of what should go where is as follows

| Folder | What should go here |
| -------------------------------- | ------------------------------------------------------------- |
| Components | individual components in separate folders that include all assets per component |
| Actions | Action creators & api calling code |
| Reducers | one file per reducer added here. import & add the reference to the combined reducers object |
| Shared | any code that can be reused for multiple components. This includes images, sass, css, js |
| Server | used by Webpack Dev Server |
| Webpack | preprocessor configurations |

Once you create your base component the reference to this needs to be added to the react-router in `elastic-erga/react-boilerplate/index.js` file. Append a new route to  the `<Route path="/" component={App}></Route>` node. Make sure you map it to your imported component.

_Reasoning on this approach:_ Its assumed that the component will be loaded to a known url. The identifier for this is the url. Which when initiated by the user from the external menu or directly interacting with the address bar etc, the url is mapped to the component by react-router to then initiate the component state. References to where this is loaded to is done via a manifest file explained in the next section.

### Deployment
The management of loading the component onto the page is done through the bootstrap-component.js file. This file (upon running the deploy command) is copied to the Larvrel framework public folder and is present on every page. bootstrap-component.js does the following:

1. reads the config from manifest.json
2. creates the required root element for the component to be attached to
3. adds any link elements to the head of the page
4. appends the script element referencing the component to the body element

After creating your component, the `webpack/webpack.production.config.js` file needs to be updated with the mappings needed to display on the page. To do this add a new instance of the `DeployToPublicWebFolder()` plugin. to the plugins array.

| config name | description | value | required |
| ------------------ | ------------------------------------------------ | ------------------------- | :-----------: |
| appendTo | the element your component requires to be present on the page to attach to | css class name '.terminal' | yes |
| default | if true, the default values will be set for the component | true / false | optional |
| manifestFile | relative path to the manifest file to create or edit | e.g. '../laravel/public/build/manifest.json' | yes |
| urlMapping | the url fragment that the component should load to | string e.g. 'terminal' | yes |
| css | the name of the css output after webpack compilation | e.g. 'styles.css' | required if default = false |
| bundleName | the name of the js bundled file after webpack compilation | e.g. 'bundle.js' | required if default = false |
| destination | the path in Laravel where to add the bootstrap-components.js file | e.g. '../laravel/public/js' |


once updated run from the command line `npm run build`. Webpack will then take over, preprocess all the SASS & JS for each component, create/amend the manifest.json file(s), and deploy the bootstrap-component.js, & all the components to Laravel public folder.







