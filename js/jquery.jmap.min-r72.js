var Mapifies;if(!Mapifies)Mapifies={};Mapifies.MapObjects={};Mapifies.MapObjects.Set=function(element,options){var mapName=jQuery(element).attr('id');var thisMap=new GMap2(element);Mapifies.MapObjects[mapName]=thisMap;Mapifies.MapObjects[mapName].Options=options;return Mapifies.MapObjects[mapName];};Mapifies.MapObjects.Append=function(element,description,appending){var mapName=jQuery(element).attr('id');Mapifies.MapObjects[mapName][description]=appending;};Mapifies.MapObjects.Get=function(element){return Mapifies.MapObjects[jQuery(element).attr('id')];};Mapifies.Initialise=function(element,options,callback){function defaults(){return{'language':'en','mapType':'map','mapCenter':[55.958858,-3.162302],'mapZoom':12,'mapControl':'small','mapEnableType':false,'mapEnableOverview':false,'mapEnableDragging':true,'mapEnableInfoWindows':true,'mapEnableDoubleClickZoom':false,'mapEnableScrollZoom':false,'mapEnableSmoothZoom':false,'mapEnableGoogleBar':false,'mapEnableScaleControl':false,'mapShowjMapsIcon':false,'debugMode':false};};options=jQuery.extend(defaults(),options);if(GBrowserIsCompatible()){var thisMap=Mapifies.MapObjects.Set(element,options);var mapType=Mapifies.GetMapType(options.mapType);thisMap.setCenter(new GLatLng(options.mapCenter[0],options.mapCenter[1]),options.mapZoom,mapType);if(options.mapShowjMapsIcon){Mapifies.AddScreenOverlay(element,{'imageUrl':'http://hg.digitalspaghetti.me.uk/jmaps/raw-file/3228fade0b3c/docs/images/jmaps-mapicon.png','screenXY':[70,10],'overlayXY':[0,0],'size':[42,25]});}
switch(options.mapControl){case"small":thisMap.addControl(new GSmallMapControl());break;case"large":thisMap.addControl(new GLargeMapControl());break;};if(options.mapEnableType)
thisMap.addControl(new GMapTypeControl());if(options.mapEnableOverview)
thisMap.addControl(new GOverviewMapControl());if(!options.mapEnableDragging)
thisMap.disableDragging();if(!options.mapEnableInfoWindows)
thisMap.disableInfoWindow();if(options.mapEnableDoubleClickZoom)
thisMap.enableDoubleClickZoom();if(options.mapEnableScrollZoom)
thisMap.enableScrollWheelZoom();if(options.mapEnableSmoothZoom)
thisMap.enableContinuousZoom();if(options.mapEnableGoogleBar)
thisMap.enableGoogleBar();if(options.mapEnableScaleControl)
thisMap.addControl(new GScaleControl());if(options.debugMode)
console.log(Mapifies);if(typeof callback=='function')
return callback(thisMap,element,options);}else{jQuery(element).text('Your browser does not support Google Maps.');return false;}
return;};Mapifies.MoveTo=function(element,options,callback){function defaults(){return{'centerMethod':'normal','mapType':null,'mapCenter':[],'mapZoom':null};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);if(options.mapType)
var mapType=Mapifies.GetMapType(options.mapType);var point=new GLatLng(options.mapCenter[0],options.mapCenter[1]);switch(options.centerMethod){case'normal':thisMap.setCenter(point,options.mapZoom,mapType);break;case'pan':thisMap.panTo(point);break;}
if(typeof callback=='function')return callback(point,options);};Mapifies.SavePosition=function(element,options,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.savePosition();if(typeof callback=='function')return callback(thisMap);};Mapifies.GotoSavedPosition=function(element,options,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.returnToSavedPosition();if(typeof callback=='function')return callback(thisMap);};Mapifies.CreateKeyboardHandler=function(element,options,callback){var thisMap=Mapifies.MapObjects.Get(element);var keyboardHandler=new GKeyboardHandler(thisMap);if(typeof callback=='function')return callback(keyboardHandler);};Mapifies.CheckResize=function(element,options,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.checkResize();if(typeof callback=='function')return callback(element);};Mapifies.SearchAddress=function(element,options,callback){function defaults(){return{'query':null,'returnType':'getLatLng','cache':undefined,'countryCode':'uk'};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);if(typeof thisMap.Geocoder==='undefined'){if(typeof options.cache==='undefined'){var geoCoder=new GClientGeocoder();}else{var geoCoder=new GClientGeocoder(cache);}
Mapifies.MapObjects.Append(element,'Geocoder',geoCoder);thisMap=Mapifies.MapObjects.Get(element);}
thisMap.Geocoder[options.returnType](options.query,function(result){if(typeof callback==='function'){return callback(result,options);}});return;};Mapifies.SearchDirections=function(element,options,callback){function defaults(){return{'query':null,'panel':null,'locale':'en_GB','travelMode':'driving','avoidHighways':false,'getPolyline':true,'getSteps':true,'preserveViewport':false,'clearLastSearch':false};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var queryOptions={'locale':options.locale,'travelMode':options.travelMode,'avoidHighways':options.avoidHighways,'getPolyline':options.getPolyline,'getSteps':options.getSteps,'preserveViewport':options.preserveViewport};var panel=$(options.panel).get(0);if(typeof thisMap.Directions==='undefined'){Mapifies.MapObjects.Append(element,'Directions',new GDirections(thisMap,panel));}
GEvent.addListener(thisMap.Directions,"load",onLoad);GEvent.addListener(thisMap.Directions,"error",onError);if(options.clearLastSearch){thisMap.Directions.clear();}
thisMap.Directions.load(options.query,queryOptions);function onLoad(){if(typeof callback=='function')return callback(thisMap.Directions,options);}
function onError(){if(typeof callback=='function')return callback(thisMap.Directions,options);}
return;};Mapifies.CreateAdsManager=function(element,options,callback){function defaults(){return{'publisherId':'','maxAdsOnMap':3,'channel':0,'minZoomLevel':6}};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var adsOptions={'maxAdsOnMap':options.maxAdsOnMap,'channel':options.channel,'minZoomLevel':options.minZoomLevel}
if(typeof thisMap.AdsManager=='undefined'){Mapifies.MapObjects.Append(element,'AdsManager',new GAdsManager(thisMap,options.publisherId,adsOptions));}
if(typeof callback=='function')return callback(thisMap.AdsManager,options);};Mapifies.AddFeed=function(element,options,callback){function defaults(){return{'feedUrl':null,'mapCenter':[]};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var feed=new GGeoXml(options.feedUrl);thisMap.addOverlay(feed);if(options.mapCenter[0]&&options.mapCenter[1])
thisMap.setCenter(new GLatLng(options.mapCenter[0],options.mapCenter[1]));if(typeof callback=='function')return callback(feed,options);return;};Mapifies.RemoveFeed=function(element,feed,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(feed);if(typeof callback=='function')return callback(feed);return;};Mapifies.AddGroundOverlay=function(element,options,callback){function defaults(){return{'overlaySouthWestBounds':undefined,'overlayNorthEastBounds':undefined,'overlayImage':undefined};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var boundries=new GLatLngBounds(new GLatLng(options.overlaySouthWestBounds[0],options.overlaySouthWestBounds[1]),new GLatLng(options.overlayNorthEastBounds[0],options.overlayNorthEastBounds[1]));groundOverlay=new GGroundOverlay(options.overlayImage,boundries);thisMap.addOverlay(groundOverlay);if(typeof callback=='function')return callback(groundOverlay,options);return;};Mapifies.RemoveGroundOverlay=function(element,groundOverlay,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(groundOverlay);if(typeof callback==='function')return callback(groundOverlay);return;};Mapifies.AddMarker=function(element,options,callback){function defaults(){var values={'pointLatLng':undefined,'pointHTML':undefined,'pointOpenHTMLEvent':'click','pointIsDraggable':false,'pointIsRemovable':false,'pointRemoveEvent':'dblclick','pointMinZoom':4,'pointMaxZoom':17,'pointIcon':undefined,'centerMap':false,'centerMoveMethod':'normal','dragStart':undefined,'dragEnd':undefined};return values;};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend({},defaults(),options);var markerOptions={}
if(typeof options.pointIcon=='object')
jQuery.extend(markerOptions,{'icon':options.pointIcon});if(options.pointIsDraggable)
jQuery.extend(markerOptions,{'draggable':options.pointIsDraggable});if(options.centerMap){switch(options.centerMoveMethod){case'normal':thisMap.setCenter(new GLatLng(options.pointLatLng[0],options.pointLatLng[1]),options.pointMaxZoom);break;case'pan':thisMap.panTo(new GLatLng(options.pointLatLng[0],options.pointLatLng[1]));break;}}
var marker=new GMarker(new GLatLng(options.pointLatLng[0],options.pointLatLng[1]),markerOptions);if(options.pointHTML)
GEvent.addListener(marker,options.pointOpenHTMLEvent,function(){marker.openInfoWindowHtml(options.pointHTML,{maxContent:options.pointMaxContent,maxTitle:options.pointMaxTitle});});if(options.pointIsRemovable)
GEvent.addListener(marker,options.pointRemoveEvent,function(){thisMap.removeOverlay(marker);});if(options.dragStart)
GEvent.addListener(marker,"dragstart",options.dragStart);if(options.dragEnd)
GEvent.addListener(marker,"dragend",options.dragEnd);if(thisMap.MarkerManager){thisMap.MarkerManager.addMarker(marker,options.pointMinZoom,options.pointMaxZoom);}else{thisMap.addOverlay(marker);}
if(typeof callback=='function')return callback(marker,options);return;};Mapifies.RemoveMarker=function(element,marker,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(marker);if(typeof callback==='function')return callback(marker);return;};Mapifies.CreateMarkerManager=function(element,options,callback){function defaults(){return{'markerManager':'GMarkerManager','borderPadding':100,'maxZoom':17,'trackMarkers':false}}
var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var markerManagerOptions={'borderPadding':options.borderPadding,'maxZoom':options.maxZoom,'trackMarkers':options.trackMarkers}
var markerManager=new window[options.markerManager](thisMap,options);Mapifies.MapObjects.Append(element,'MarkerManager',markerManager);if(typeof callback=='function')return callback(markerManager,options);};Mapifies.AddPolygon=function(element,options,callback){function defaults(){return{'polygonPoints':[],'polygonStrokeColor':"#000000",'polygonStrokeWeight':5,'polygonStrokeOpacity':1,'polygonFillColor':"#ff0000",'polygonFillOpacity':1,'mapCenter':undefined,'polygonClickable':true}}
var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var polygonOptions={};if(!options.polygonClickable)
polygonOptions=jQuery.extend(polygonOptions,{clickable:false});if(typeof options.mapCenter!=='undefined'&&options.mapCenter[0]&&options.mapCenter[1])
thisMap.setCenter(new GLatLng(options.mapCenter[0],options.mapCenter[1]));var allPoints=[];jQuery.each(options.polygonPoints,function(i,point){allPoints.push(new GLatLng(point[0],point[1]));});var polygon=new GPolygon(allPoints,options.polygonStrokeColor,options.polygonStrokeWeight,options.polygonStrokeOpacity,options.polygonFillColor,options.polygonFillOpacity,polygonOptions);thisMap.addOverlay(polygon);if(typeof callback=='function')return callback(polygon,polygonOptions,options);return;}
Mapifies.RemovePolygon=function(element,polygon,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(polygon);if(typeof callback==='function')return callback(polygon);return;};Mapifies.AddPolyline=function(element,options,callback){function defaults(){return{'polylinePoints':[],'polylineStrokeColor':"#ff0000",'polylineStrokeWidth':10,'polylineStrokeOpacity':1,'mapCenter':[],'polylineGeodesic':false,'polylineClickable':true};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var polyLineOptions={};if(options.polylineGeodesic)
jQuery.extend(polyLineOptions,{geodesic:true});if(!options.polylineClickable)
jQuery.extend(polyLineOptions,{clickable:false});if(options.mapCenter[0]&&options.mapCenter[1])
thisMap.setCenter(new GLatLng(options.mapCenter[0],options.mapCenter[1]));var allPoints=[];jQuery.each(options.polylinePoints,function(i,point){allPoints.push(new GLatLng(point[0],point[1]));});var polyline=new GPolyline(allPoints,options.polylineStrokeColor,options.polylineStrokeWidth,options.polylineStrokeOpacity,polyLineOptions);thisMap.addOverlay(polyline);if(typeof callback=='function')return callback(polyline,polyLineOptions,options);return;}
Mapifies.RemovePolyline=function(element,polyline,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(polyline);if(typeof callback==='function')return callback(polyline);return;};Mapifies.AddScreenOverlay=function(element,options,callback){function defaults(){return{'imageUrl':'','screenXY':[],'overlayXY':[],'size':[]};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var overlay=new GScreenOverlay(options.imageUrl,new GScreenPoint(options.screenXY[0],options.screenXY[1]),new GScreenPoint(options.overlayXY[0],options.overlayXY[1]),new GScreenSize(options.size[0],options.size[1]));thisMap.addOverlay(overlay);if(typeof callback=='function')return callback(overlay,options);};Mapifies.RemoveScreenOverlay=function(element,overlay,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(overlay);if(typeof callback==='function')return callback(overlay);return;};Mapifies.CreateStreetviewPanorama=function(element,options,callback){function defaults(){return{'overideContainer':'','latlng':[40.75271883902363,-73.98262023925781],'pov':[]}};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var container=null;if(options.overideContainer!==''){container=jQuery(options.overideContainer).get(0);}else{container=jQuery(element).get(0);}
var viewOptions={};if(options.pov.length>0){jQuery.extend(viewOptions,{'pov':new GPov(options.latlng[0],options.latlng[1],options.latlng[2])});}
if(options.latlng.length>0){jQuery.extend(viewOptions,{'latlng':new GLatLng(options.latlng[0],options.latlng[1])});}
var overlay=new GStreetviewPanorama(container,viewOptions);if(typeof callback=='function')return callback(overlay,options);return;};Mapifies.RemoveStreetviewPanorama=function(element,view,callback){var thisMap=Mapifies.MapObjects.Get(element);view.remove();if(typeof callback=='function')return callback(view);return;};Mapifies.AddTrafficInfo=function(element,options,callback){function defaults(){return{'mapCenter':[]};};var thisMap=Mapifies.MapObjects.Get(element);options=jQuery.extend(defaults(),options);var trafficOverlay=new GTrafficOverlay;thisMap.addOverlay(trafficOverlay);if(options.mapCenter[0]&&options.mapCenter[1]){thisMap.setCenter(new GLatLng(options.mapCenter[0],options.mapCenter[1]));}
if(typeof callback=='function')return callback(trafficOverlay,options);};Mapifies.RemoveTrafficInfo=function(element,trafficOverlay,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.removeOverlay(trafficOverlay);if(typeof callback==='function')return callback(trafficOverlay);return;};Mapifies.SearchCode=function(code){switch(code){case G_GEO_SUCCESS:return{'code':G_GEO_SUCCESS,'success':true,'message':'Success'};case G_GEO_UNKNOWN_ADDRESS:return{'code':G_GEO_UNKNOWN_ADDRESS,'success':false,'message':'No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect'};break;case G_GEO_SERVER_ERROR:return{'code':G_GEO_UNKNOWN_ADDRESS,'success':false,'message':'A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.'};break;case G_GEO_MISSING_QUERY:return{'code':G_GEO_UNKNOWN_ADDRESS,'success':false,'message':'The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.'};break;case G_GEO_BAD_KEY:return{'code':G_GEO_UNKNOWN_ADDRESS,'success':false,'message':'The given key is either invalid or does not match the domain for which it was given.'};break;case G_GEO_BAD_REQUEST:return{'code':G_GEO_UNKNOWN_ADDRESS,'success':false,'message':'A directions request could not be successfully parsed.'};break;default:return{'code':null,'success':false,'message':'An unknown error occurred.'};break;};}
Mapifies.GetMapType=function(mapType){switch(mapType){case'map':mapType=G_NORMAL_MAP;break;case'sat':mapType=G_SATELLITE_MAP;break;case'hybrid':mapType=G_HYBRID_MAP;break;};return mapType;};Mapifies.GetTravelMode=function(travelMode){switch(travelMode){case'driving':travelMode=G_TRAVEL_MODE_DRIVING;break;case'walking':travelMode=G_TRAVEL_MODE_WALKING;break;};return travelMode;};Mapifies.createIcon=function(options){function defaults(){return{'iconImage':undefined,'iconShadow':undefined,'iconSize':undefined,'iconShadowSize':undefined,'iconAnchor':undefined,'iconInfoWindowAnchor':undefined,'iconPrintImage':undefined,'iconMozPrintImage':undefined,'iconPrintShadow':undefined,'iconTransparent':undefined};};options=jQuery.extend(defaults(),options);var icon=new GIcon(G_DEFAULT_ICON);if(options.iconImage)
icon.image=options.iconImage;if(options.iconShadow)
icon.shadow=options.iconShadow;if(options.iconSize)
icon.iconSize=options.iconSize;if(options.iconShadowSize)
icon.shadowSize=options.iconShadowSize;if(options.iconAnchor)
icon.iconAnchor=options.iconAnchor;if(options.iconInfoWindowAnchor)
icon.infoWindowAnchor=options.iconInfoWindowAnchor;return icon;};Mapifies.getCenter=function(element){var thisMap=Mapifies.MapObjects.Get(element);return thisMap.getCenter();};Mapifies.clearOverlays=function(element,callback){var thisMap=Mapifies.MapObjects.Get(element);thisMap.clearOverlays();if(typeof callback==='function')return callback();return;};Mapifies.getBounds=function(element){var thisMap=Mapifies.MapObjects.Get(element);return thisMap.getBounds();};var Mapifies;if(!Mapifies)Mapifies={};(function($){$.fn.jmap=function(method,options,callback){return this.each(function(){if(method=='init'&&typeof options=='undefined'){new Mapifies.Initialise(this,{},null);}else if(method=='init'&&typeof options=='object'){new Mapifies.Initialise(this,options,callback);}else if(method=='init'&&typeof options=='function'){new Mapifies.Initialise(this,{},options);}else if(typeof method=='object'||method==null){new Mapifies.Initialise(this,method,options);}else{try{new Mapifies[method](this,options,callback);}catch(err){throw Error('Mapifies Function Does Not Exist');}}});}})(jQuery);