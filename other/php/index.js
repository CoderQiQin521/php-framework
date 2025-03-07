"use strict";(globalThis.webpackChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(32769),i=o(73968),l=o(69942),c=o(11117),m=o(67294),p=o(34493),u=o(90874);function h({children:e}){let r=(0,i.T)(),[o]=m.useState(r?.repo),[t]=m.useState(r?.currentUser),_=(0,m.useMemo)(()=>({}),[]);return(0,m.useEffect)(()=>{let e=document.querySelector(".footer");e&&(e.querySelector(".mt-6")?.classList.replace("mt-6","mt-0"),e.querySelector(".border-top")?.classList.remove("border-top"))},[]),(0,a.jsxs)(a.Fragment,{children:[(0,a.jsx)("meta",{"data-hydrostats":"publish"}),(0,a.jsx)(l.xp,{initialValue:l._G.xxxlarge,children:(0,a.jsx)(d.n,{appName:"react-code-view",category:"",metadata:_,children:(0,a.jsx)(u.M,{user:t,children:(0,a.jsx)(n.d,{repository:o,children:(0,a.jsx)(c.K,{children:(0,a.jsx)(p.o,{children:e})})})})})})]})}try{(t=h).displayName||(t.displayName="App")}catch{}var b=o(54094);(0,s.g)("react-code-view",()=>({App:h,routes:[(0,_.g)({path:"/:owner/:repo/tree/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/blob/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/blame/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/edit/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/new/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/new/:branch/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/tree/delete/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/delete/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/tree/:branch/*",Component:b.Z,shouldNavigateOnError:!0})]}))},77083:(e,r,o)=>{o.d(r,{g:()=>t});function t({path:e,Component:r,shouldNavigateOnError:o,transitionType:t}){async function _({location:e}){let r;try{let o=`${e.pathname}${e.search}`;r=await window.fetch(o,{headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest","X-GitHub-Target":"dotcom"}})}catch(e){return{type:o?"route-handled-error":"error",error:{type:"fetchError"}}}if(r.redirected)return{type:"redirect",url:r.url};if(!r.ok)return{type:o?"route-handled-error":"error",error:{type:"httpError",httpStatus:r.status}};try{let e=await r.json();return{type:"loaded",data:e,title:e.title}}catch(e){return{type:o?"route-handled-error":"error",error:{type:"badResponseError"}}}}return{path:e,Component:r,coreLoader:_,loadFromEmbeddedData:function({embeddedData:e}){return{data:e,title:e.title}},transitionType:t}}}},e=>{var r=r=>e(e.s=r);e.O(0,["react-lib","vendors-node_modules_dompurify_dist_purify_js","vendors-node_modules_stacktrace-parser_dist_stack-trace-parser_esm_js-node_modules_github_bro-a4c183","vendors-node_modules_primer_octicons-react_dist_index_esm_js-node_modules_primer_react_lib-es-3db9ab","vendors-node_modules_primer_react_lib-esm_Box_Box_js","vendors-node_modules_primer_react_lib-esm_Button_Button_js-node_modules_primer_react_lib-esm_-f6da63","vendors-node_modules_primer_react_lib-esm_Truncate_Truncate_js-node_modules_primer_react_lib--5d1957","vendors-node_modules_primer_behaviors_dist_esm_focus-zone_js","vendors-node_modules_primer_react_lib-esm_Button_index_js-node_modules_primer_react_lib-esm_O-279bf8","vendors-node_modules_primer_react_lib-esm_Text_Text_js-node_modules_primer_react_lib-esm_Text-85a14b","vendors-node_modules_primer_react_lib-esm_ActionList_index_js","vendors-node_modules_primer_react_lib-esm_ActionMenu_ActionMenu_js","vendors-node_modules_primer_behaviors_dist_esm_scroll-into-view_js-node_modules_primer_react_-04bb1b","vendors-node_modules_primer_react_lib-esm_FormControl_FormControl_js","vendors-node_modules_react-router-dom_dist_index_js","vendors-node_modules_github_relative-time-element_dist_index_js","vendors-node_modules_primer_react_lib-esm_PageLayout_PageLayout_js-node_modules_github_hydro--f8521d","vendors-node_modules_github_mini-throttle_dist_index_js-node_modules_github_alive-client_dist-bf5aa2","vendors-node_modules_primer_react_lib-esm_Dialog_js-node_modules_primer_react_lib-esm_Flash_F-54f402","vendors-node_modules_primer_react_lib-esm_UnderlineNav2_index_js","vendors-node_modules_primer_react_lib-esm_Avatar_Avatar_js-node_modules_primer_react_lib-esm_-9bd36c","vendors-node_modules_primer_react_lib-esm_AvatarStack_AvatarStack_js-node_modules_primer_reac-6d3540","vendors-node_modules_primer_react_lib-esm_Breadcrumbs_Breadcrumbs_js-node_modules_primer_reac-2cc1c1","ui_packages_soft-nav_soft-nav_ts","ui_packages_react-core_create-browser-history_ts-ui_packages_react-core_deferred-registry_ts--ebbb92","ui_packages_react-core_register-app_ts","ui_packages_ref-selector_RefSelector_tsx","ui_packages_alive_alive_ts-ui_packages_alive_connect-alive-subscription_ts","app_assets_modules_github_blob-anchor_ts-app_assets_modules_github_filter-sort_ts-app_assets_-e50ab6","app_assets_modules_react-code-view_pages_CodeView_tsx"],()=>r(10089));var o=e.O()}]);
//# sourceMappingURL=react-code-view-33a3d3b514d3.js.map

console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])
'Thank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hardThank yourself for trying so hard'

function hello() {
}
ckChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(3276
console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])

function hello() {
}
                                                                                const arr = [1,2,3,4];
console.log(arr[1])

function hello() {
}
console.log(1234567)

console.log(1234567)
const arr = [1,2,3,4];
console.log(arr[1])


const arr = [1,2,3,4];
console.log(arr[1])

function hello() {
}
"use strict";(globalThis.webpackChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(32769),i=o(73968),l=o(69942),c=o(11117),m=o(67294),p=o(34493),u=o(90874);function h({children:e}){let r=(0,i.T)(),[o]=m.useState(r?.repo),[t]=m.useState(r?.currentUser),_=(0,m.useMemo)(()=>({}),[]);return(0,m.useEffect)(()=>{let e=document.querySelector(".footer");e&&(e.querySelector(".mt-6")?.classList.replace("mt-6","mt-0"),e.querySelector(".border-top")?.classList.remove("border-top"))},[]),(0,a.jsxs)(a.Fragment,{children:[(0,a.jsx)("meta",{"data-hydrostats":"publish"}),(0,a.jsx)(l.xp,{initialValue:l._G.xxxlarge,children:(0,a.jsx)(d.n,{appName:"react-code-view",category:"",metadata:_,children:(0,a.jsx)(u.M,{user:t,children:(0,a.jsx)(n.d,{repository:o,children:(0,a.jsx)(c.K,{children:(0,a.jsx)(p.o,{children:e})})})})})})]})}try{(t=h).displayName||(t.displayName="App")}catch{}var b=o(54094);(0,s.g)("react-code-view",()=>({App:h,routes:[(0,_.g)({path:"/:owner/:repo/tree/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/blob/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/blame/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/edit/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/new/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/new/:branch/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/tree/delete/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/delete/:branch/:path/*",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo",Component:b.Z,shouldNavigateOnError:!0}),(0,_.g)({path:"/:owner/:repo/tree/:branch/*",Component:b.Z,shouldNavigateOnError:!0})]}))},77083:(e,r,o)=>{o.d(r,{g:()=>t});function t({path:e,Component:r,shouldNavigateOnError:o,transitionType:t}){async function _({location:e}){let r;try{let o=`${e.pathname}${e.search}`;r=await window.fetch(o,{headers:{Accept:"application/json","X-Requested-With":"XMLHttpRequest","X-GitHub-Target":"dotcom"}})}catch(e){return{type:o?"route-handled-error":"error",error:{type:"fetchError"}}}if(r.redirected)return{type:"redirect",url:r.url};if(!r.ok)return{type:o?"route-handled-error":"error",error:{type:"httpError",httpStatus:r.status}};try{let e=await r.json();return{type:"loaded",data:e,title:e.title}}catch(e){return{type:o?"route-handled-error":"error",error:{type:"badResponseError"}}}}return{path:e,Component:r,coreLoader:_,loadFromEmbeddedData:function({embeddedData:e}){return{data:e,title:e.title}},transitionType:t}}}},e=>{var r=r=>e(e.s=r);e.O(0,["react-lib","vendors-node_modules_dompurify_dist_purify_js","vendors-node_modules_stacktrace-parser_dist_stack-trace-parser_esm_js-node_modules_github_bro-a4c183","vendors-node_modules_primer_octicons-react_dist_index_esm_js-node_modules_primer_react_lib-es-3db9ab","vendors-node_modules_primer_react_lib-esm_Box_Box_js","vendors-node_modules_primer_react_lib-esm_Button_Button_js-node_modules_primer_react_lib-esm_-f6da63","vendors-node_modules_primer_react_lib-esm_Truncate_Truncate_js-node_modules_primer_react_lib--5d1957","vendors-node_modules_primer_behaviors_dist_esm_focus-zone_js","vendors-node_modules_primer_react_lib-esm_Button_index_js-node_modules_primer_react_lib-esm_O-279bf8","vendors-node_modules_primer_react_lib-esm_Text_Text_js-node_modules_primer_react_lib-esm_Text-85a14b","vendors-node_modules_primer_react_lib-esm_ActionList_index_js","vendors-node_modules_primer_react_lib-esm_ActionMenu_ActionMenu_js","vendors-node_modules_primer_behaviors_dist_esm_scroll-into-view_js-node_modules_primer_react_-04bb1b","vendors-node_modules_primer_react_lib-esm_FormControl_FormControl_js","vendors-node_modules_react-router-dom_dist_index_js","vendors-node_modules_github_relative-time-element_dist_index_js","vendors-node_modules_primer_react_lib-esm_PageLayout_PageLayout_js-node_modules_github_hydro--f8521d","vendors-node_modules_github_mini-throttle_dist_index_js-node_modules_github_alive-client_dist-bf5aa2","vendors-node_modules_primer_react_lib-esm_Dialog_js-node_modules_primer_react_lib-esm_Flash_F-54f402","vendors-node_modules_primer_react_lib-esm_UnderlineNav2_index_js","vendors-node_modules_primer_react_lib-esm_Avatar_Avatar_js-node_modules_primer_react_lib-esm_-9bd36c","vendors-node_modules_primer_react_lib-esm_AvatarStack_AvatarStack_js-node_modules_primer_reac-6d3540","vendors-node_modules_primer_react_lib-esm_Breadcrumbs_Breadcrumbs_js-node_modules_primer_reac-2cc1c1","ui_packages_soft-nav_soft-nav_ts","ui_packages_react-core_create-browser-history_ts-ui_packages_react-core_deferred-registry_ts--ebbb92","ui_packages_react-core_register-app_ts","ui_packages_ref-selector_RefSelector_tsx","ui_packages_alive_alive_ts-ui_packages_alive_connect-alive-subscription_ts","app_assets_modules_github_blob-anchor_ts-app_assets_modules_github_filter-sort_ts-app_assets_-e50ab6","app_assets_modules_react-code-view_pages_CodeView_tsx"],()=>r(10089));var o=e.O()}]);
console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])

function hello() {
}
ckChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(3276
console.log(1234567)console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])

function hello() {
}
ckChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(3276
console.log(1234567)
                                                                                function hello() {
}
ckChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(3276
console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])
ckChunk=globalThis.webpackChunk||[]).push([["react-code-view"],{10089:(e,r,o)=>{var t,_=o(77083),s=o(79953),a=o(85893),d=o(34232),n=o(3276
console.log(1234567)
                                                                                console.log(1234567)

const arr = [1,2,3,4];
console.log(arr[1])
