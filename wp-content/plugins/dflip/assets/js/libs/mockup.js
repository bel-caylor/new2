/**
 * @preserve Mockup 1.01 | Copyright 2016, Deepak Ghimire.  All rights reserved.
 */

"use strict";


var MOCKUP = {VERSION: '1', REVISION: '01'};

//Paper and it's related objects
(function (MOCKUP) {

  MOCKUP.__extends = function (child, parent) {
    for (var key in parent)
      if (parent.hasOwnProperty(key)) child[key] = parent[key];
    function Ctor() {
      this.constructor = child;
    }

    Ctor.prototype = parent.prototype;
    child.prototype = new Ctor();
    child.__super = parent.prototype;
    return child;
  };

  //type of geometry for paper is either a plane or box
  MOCKUP.MODE = {PLUGIN: 0, BUILDER: 1};
  //MOCKUP.mode = MOCKUP.MODE.PLUGIN;
  MOCKUP.GEOMETRY_TYPE = {PLANE: 0, BOX: 1, MODEL: 2};
  //if geometry is box then the front material index is number 4 and back is number 5
  MOCKUP.MATERIAL_FACE = {FRONT: 4, BACK: 5};
  MOCKUP.MOUSE = {LEFT: 0, MIDDLE: 1, RIGHT: 2};

  MOCKUP.Vector3 = THREE.Vector3;
  MOCKUP.Vector2 = THREE.Vector2;
  MOCKUP.defaults = {
    anisotropy: 8,
    maxTextureSize: 2048,
    groundTexture: "images/textures/ground-grid.png",
    textureLoadFallback: "blank"
  };
  //use this is you are creating templates and want to save things as template
  MOCKUP.overrideAsTemplate = false;
  MOCKUP.enableLog = false;

  /*
   MOCKUP.overrideAsTemplate = true;
   MOCKUP.enableLog = true;
   */

  //log handlers
  MOCKUP.log = function () {
    console.log.apply(console, arguments);
  };

  MOCKUP.error = function () {
    console.error.apply(console, arguments);
  };

  MOCKUP.autoSave = function () {
    //you can customize to save any instance.
  };
  MOCKUP.libType = "THREE";
  MOCKUP.textureLoader = new THREE.TextureLoader();
  //default settings for paper objects
  MOCKUP.paperDefaults = {
    geometryType: MOCKUP.GEOMETRY_TYPE.PLANE,
    width: 210,
    height: 297,
    depth: 0.2,
    segments: 150,
    widthScale: 1,
    heightScale: 1,
    folds: 1,
    angles: [],
    backImage: null,
    frontImage: null,
    frontBump: null,
    backBump: null,
    mipmap: false,
    shininess: 15,
    bumpScale: 0.4,
    stiffness: 0.02,
    color: 0xffffff,
    skipMaterials: false,
    repeat: 100,
    defaultImage: [, , , , "blank", "blank"]
  };

  MOCKUP.Paper = (function (_super) {
    MOCKUP.__extends(Paper, _super);

    /**
     * Paper, a fundamental object in MOCKUP, tries to integrate paper behaviour
     * @param parameters settings from the derived objects
     * @param stage not used yet..
     * @constructor
     */

    function Paper(parameters, stage) {
      parameters = jQuery.extend({}, MOCKUP.paperDefaults, parameters);

      this.geometryType = parameters.geometryType;
      this.width = parameters.width;
      this.widthScale = parameters.widthScale;
      this.height = parameters.height;
      this.depth = parameters.depth;
      this.segments = parameters.segments;
      this.folds = parameters.folds;
      this.angles = parameters.angles;

      this._shininess = parameters.shininess;
      this._bumpScale = parameters.bumpScale;
      this.stiffness = parameters.stiffness;
      this.color = parameters.color;
      this.heightScale = parameters.heightScale;
      this.defaultImage = parameters.defaultImage;

      this.baseType = "Paper";
      this.type = "Paper";
      this.subType = parameters.subType;
      if (parameters.cloneParent != null) this.cloneParent = parameters.cloneParent;
      this.createGeometry();
      this.updateGeometry();

      //texture are initialized by respective functions
      if (!parameters.skipMaterials) {
        if (!(parameters.skipLoad == true && parameters.cloneParent != null)) {
          if (this.frontImage != null && parameters.defaultImage != null) this.frontImage(parameters.frontImage != null
            ? parameters.frontImage : parameters.defaultImage[MOCKUP.MATERIAL_FACE.FRONT]);
          if (this.backImage != null && parameters.defaultImage != null) this.backImage(parameters.backImage != null
            ? parameters.backImage : parameters.defaultImage[MOCKUP.MATERIAL_FACE.BACK]);
          if (this.frontBump != null) this.frontBump(parameters.frontBump);
          if (this.backBump != null) this.backBump(parameters.backBump);
        }
      }
      if (this.bumpScale != null) this.bumpScale(parameters.bumpScale);
      //this is future implementation. to follow babylon structure
      if (stage != null) if (stage.add != null) stage.add(this);
    }

    //load image to texture
    var loadTexture = function (texture, object, mapType, faceNumber) {
      if (texture) {
        var img = texture.image;
        texture.naturalWidth = img.naturalWidth;
        texture.naturalHeight = img.naturalHeight;
        texture.needsUpdate = true;
      }
      if (texture !== null && mapType == "map") {
        texture.anisotropy = 0;
        if (MOCKUP.defaults.anisotropy > 0)
          texture.anisotropy = MOCKUP.defaults.anisotropy;
        if (THREE.skipPowerOfTwo == true) {
          texture.minFilter = THREE.LinearFilter;
          texture.magFilter = THREE.LinearFilter;
        }
        texture.name = new Date().toTimeString();
        //console.log(texture.name, image);
        //this is to settle issues with Ground repeat.
        if (object.textureRepeat != null) {

          texture.wrapS = THREE.RepeatWrapping;
          texture.wrapT = THREE.RepeatWrapping;
          if (MOCKUP.defaults.anisotropy > 0)
            texture.anisotropy = MOCKUP.defaults.anisotropy;
          texture.repeat.set(object._textureRepeat, object._textureRepeat);

          if (object instanceof MOCKUP.Ground) {
            object.material.bumpMap = texture;
            object.material.bumpScale = object.bumpScale();
            object.material.needsUpdate = true;
          }
          //object.materials[faceNumber].needsUpdate = true;
        }
      }

      if (object.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE) {
        clearTexture(object.material[mapType]);
        object.material[mapType] = texture;
        if (mapType == "bumpMap")
          object.material.bumpScale = object.bumpScale();
        //object.material.needsUpdate = true;
      }
      else {//if (object.geometryType == MOCKUP.GEOMETRY_TYPE.BOX) {
        clearTexture(object.material.materials[faceNumber][mapType]);
        object.material.materials[faceNumber][mapType] = texture;
        if (mapType == "bumpMap")
          object.material.materials[faceNumber].bumpScale = object.bumpScale();
        //object.material.materials[faceNumber].needsUpdate = true;
        object.material.materials[faceNumber].needsUpdate = true;
      }

      MOCKUP.autoSave();
      //if (MOCKUP.autoSave) MOCKUP.autoSave();
    };
    //load image to texture
    var loadTextureFailed = function (event, image) {
      //console.log("Failed to load texture:" + image);
      return null;
    };

    var clearTexture = function (texture) {
      if (texture) {
        if (texture.image) {
          if (texture.image.nodeName == "CANVAS") {
            if (texture.image.remove)
              texture.image.remove();
            delete texture.image;
          }
        }
        if (texture.dispose) {
          texture.dispose();
        }
        texture = null;
      }
    }

    var clearObjectTexture = function (object, mapType, faceNumber) {
      if (object.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE) {
        clearTexture(object.material[mapType]);
      }
      else {//if (object.geometryType == MOCKUP.GEOMETRY_TYPE.BOX) {
        clearTexture(object.material.materials[faceNumber][mapType]);
      }
    }
    var traverseStage = function (object) {
      if (!(object instanceof THREE.Object3D) || !(object.parent instanceof THREE.Object3D)) return null;
      if (object.parent instanceof THREE.Scene) return object.parent;
      return traverseStage(object.parent);
    };
    /**
     * Load images to a texture and returns the texture, works for different maps
     * @param object object where image is to be loaded
     * @param image Image to be loaded
     * @param faceNumber Facenumber if the material is a facematerial
     * @param mapType Type of map e.g. map, bumpMap, etc.
     */

    MOCKUP.loadImage = function (object, image, faceNumber, mapType, callback) {
      //console.log(image);
      if (image == null) {//get value
        var value = (object.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE)
          ? (object.material[mapType] !== null) ? object.material[mapType].sourceFile : null
          : (object.material.materials[faceNumber] == null) ? null
                      : (object.material.materials[faceNumber][mapType])
              ? object.material.materials[faceNumber][mapType].sourceFile : null;

        return (value == null) ? null : (value.indexOf("data:image") > -1) ? null : value;
      }

      else {
        //set value
        //loadTexture: function ( url, mapping, onLoad, onError )
        if (MOCKUP.enableLog == true) console.log(image);
        var _texture = null;

        if (image.nodeName == "CANVAS" || image.nodeName == "IMG") {
          _texture = new THREE.Texture(image);
          _texture.needsUpdate = true;
          loadTexture(_texture, object, mapType, faceNumber);
          if (callback != null) callback(object, _texture);
          var possibleStage = traverseStage(object);
          if (possibleStage != null) possibleStage.renderRequestPending = true;
        }
        else {
          if (image != "blank") {
            _texture = (image == null) ? null : MOCKUP.textureLoader.load(image,// THREE.UVMapping,
              function (texture) {
                texture.sourceFile = image;
                loadTexture(texture, object, mapType, faceNumber);
                if (callback != null) callback(object, texture);
                var possibleStage = traverseStage(object);
                if (possibleStage != null) possibleStage.renderRequestPending = true;
              }, void 0, //the other end expects explicitly void 0
              function (event) {
                if (_texture.image == null) {
                  if (object.defaultImage != null) {
                    var defaultImage = (object.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE)
                      ? object.defaultImage[MOCKUP.MATERIAL_FACE.FRONT]
                      : object.defaultImage[faceNumber];
                    if (image !== defaultImage && defaultImage != null) {
                      MOCKUP.loadImage(object, defaultImage, faceNumber, mapType);
                    }
                    else if (image !== MOCKUP.defaults.textureLoadFallback || defaultImage == null) {
                      MOCKUP.loadImage(object, MOCKUP.defaults.textureLoadFallback, faceNumber, mapType);
                    }
                  }
                  else if (image !== MOCKUP.defaults.textureLoadFallback) {
                    MOCKUP.loadImage(object, MOCKUP.defaults.textureLoadFallback, faceNumber, mapType);
                  }
                }
                loadTextureFailed(event, image);
              });
            if (_texture) _texture.mapping = THREE.UVMapping;
          }
          else {
            loadTexture(null, object, mapType, faceNumber);
          }
        }

        return 0;
      }
    };

    Paper.prototype.loadImage = function (image, faceNumber, mapType, callback) {
      return MOCKUP.loadImage(this, image, faceNumber, mapType, callback);
    };

    //Properties
    Paper.prototype.frontImage = function (frontImage, callback) {
      return this.loadImage(frontImage, MOCKUP.MATERIAL_FACE.FRONT, "map", callback);
    };
    Paper.prototype.backImage = function (backImage, callback) {
      return this.loadImage(backImage, MOCKUP.MATERIAL_FACE.BACK, "map", callback);
    };
    Paper.prototype.frontBump = function (frontBump, callback) {
      return this.loadImage(frontBump, MOCKUP.MATERIAL_FACE.FRONT, "bumpMap", callback);
    };
    Paper.prototype.backBump = function (backBump, callback) {
      return this.loadImage(backBump, MOCKUP.MATERIAL_FACE.BACK, "bumpMap", callback);
    };
    Paper.prototype.bumpScale = function (bumpScale) {
      if (bumpScale == null) {//get value
        return this._bumpScale;
      }
      else {//set value
        this._bumpScale = bumpScale;
        if (this.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE) {
          this.material.bumpScale = bumpScale;
        }
        else if (this.geometryType == MOCKUP.GEOMETRY_TYPE.BOX) {
          this.material.materials[MOCKUP.MATERIAL_FACE.FRONT].bumpScale = bumpScale;
          this.material.materials[MOCKUP.MATERIAL_FACE.BACK].bumpScale = bumpScale;
        }
      }
    };
    Paper.prototype.shininess = function (shininess) {
      if (shininess == null) {//get value
        return this._shininess;
      }
      else {//set value
        this._shininess = shininess;
        if (this.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE) {
          this.material.shininess = shininess;
        }
        else if (this.geometryType == MOCKUP.GEOMETRY_TYPE.BOX) {
          this.material.materials[MOCKUP.MATERIAL_FACE.FRONT].shininess = shininess;
          this.material.materials[MOCKUP.MATERIAL_FACE.BACK].shininess = shininess;
        }
      }
    };
    Paper.prototype.bumpImage = function (bumpImage) {
      return this.loadImage(bumpImage, MOCKUP.MATERIAL_FACE.FRONT, "bumpMap");
    };
    //Methods

    /**
     * Override the clone function of Mesh Object
     */
    Paper.prototype.createCopy = function (parameters) {
      if (parameters == null) {
        parameters = {};
        MOCKUP.getParameter(this, parameters)
      }
      parameters.type = this.type;
      var object = MOCKUP.createObject(parameters);
      //var object = new THREE.Mesh( this.geometry, this.material );
      object.cloneParent = null;
      object.copy(this, null);

      return object;
    };

    Paper.prototype.createClone = function (parameters) {
      if (parameters == null) {
        parameters = {};
        MOCKUP.getParameter(this, parameters)
      }
      parameters.type = this.type;
      var object = MOCKUP.createObject(parameters);
      object.material = this.material;
      object.cloneParent = (object.cloneParent != null) ? object.cloneParent : this.uuid;
      //var object = new THREE.Mesh( this.geometry, this.material );

      object.copy(this, null);

      return object;
    };

    Paper.prototype.createGeometry = function () {
      var _matParameters = {
        color: this.color,
        shading: THREE.SmoothShading,
        shininess: this._shininess
      };
      var _material = new THREE.MeshPhongMaterial(_matParameters);

      if (this.geometryType == MOCKUP.GEOMETRY_TYPE.BOX) {
        var _materials = [_material, _material, _material, _material,
          new THREE.MeshPhongMaterial(_matParameters), new THREE.MeshPhongMaterial(_matParameters)];
        _super.call(this,
          new THREE.BoxGeometry(this.width, this.height, this.depth, this.segments * this.folds, 1, 1),
          new THREE.MeshFaceMaterial(_materials));
      }
      else if (this.geometryType == MOCKUP.GEOMETRY_TYPE.PLANE) {
        _super.call(this,
          new THREE.PlaneBufferGeometry(this.width, this.height),
          _material);
      }
    };
    Paper.prototype.updateGeometry = function () {
      //console.log("updated normal paper");
    };
    return Paper;
  })(THREE.Mesh);

})(MOCKUP || (MOCKUP = {}));

//Stage and other objects
(function (MOCKUP) {

  MOCKUP.selected = null;

  MOCKUP.Stage = (function (_super) {
    MOCKUP.__extends(Stage, _super);
    function Stage(parameters) {
      parameters = parameters || {};
      var _this = this;
      _this.postRender = null;
      _super.call(_this);
      if (parameters.skipLoad !== true) {
        //currently canvas is compulsory
        _this.canvas = parameters.canvas || document.createElement('canvas');

        //check if context was lost
        /*                _this.canvas.addEventListener("webglcontextlost", function (event) {
         event.preventDefault();
         // animationID would have been set by your call to requestAnimationFrame
         console.log("Context Lost");
         if (window.swal != null)
         swal({
         title: "WebGL Context Lost!",
         text: "The WebGl has crashed. You'll need to restart the application.. and maybe Google Chrome too..",
         type: "warning",
         confirmButtonColor: editor.colors.red,
         confirmButtonText: "OK!",
         closeOnConfirm: true
         });
         cancelRequestAnimationFrame(animate);
         }, false);*/

        /*                _this.canvas.addEventListener("webglcontextrestored", function (event) {
         // Do something
         console.log("Context Restore");
         }, false);*/

        _this.canvas = jQuery(this.canvas);

        //_this.height = _this.canvas.parent().height();
        //_this.width = _this.canvas.parent().width();

        _this.camera = new THREE.PerspectiveCamera(30, _this.width / _this.height, 4, 50000);
        _this.renderer = new THREE.WebGLRenderer({canvas: _this.canvas[0], antialias: true, alpha: true});

        _this.renderer.setPixelRatio(parameters.pixelRatio);
        _this.renderer.setSize(_this.width, _this.height);
        _this.renderer.setClearColor(0xffffff, 0);

        var orbitControl = _this.orbitControl = new THREE.OrbitControls(_this.camera, _this.renderer.domElement);
        orbitControl.maxPolarAngle = Math.PI;

        //this.renderer.shadowMapDebug = true;
        //this.renderer.shadowMapCullFace = true;
        if (MOCKUP.mode !== MOCKUP.MODE.PLUGIN) {
          _this.renderer.shadowMap.enabled = true;

          _this.ground = new MOCKUP.Ground({
            color: 0xeeeeee,
            height: _this.camera.far,
            width: _this.camera.far
          }, _this);

          //_this.add(_this.ground);
          //_this.ground.rotation.x = -Math.PI / 2;
        }
        _this.ambientLight = new THREE.AmbientLight(0x444444);
        _this.add(_this.ambientLight);
        //_this.fog = new THREE.Fog(0xffffff, 3000, 5000);

        //sportlights cause gradient in pages so switching to directional light
        var spotLight = _this.spotLight = new THREE.DirectionalLight(0xffffff, 0.5);

        spotLight.position.set(0, 1, 0);

        if (parameters.webglShadow != false) {
          spotLight.castShadow = true;
          spotLight.shadow = new THREE.LightShadow(new THREE.PerspectiveCamera(70, 1, 200, 2000));
          spotLight.shadow.bias = -0.000222;
          spotLight.shadow.mapSize.width = 1024;
          spotLight.shadow.mapSize.height = 1024;
        }
        spotLight.intensity = 0.6;

        // spotLight.castShadow = true;
        if (spotLight.shadow) {
          var spotLightHelper = _this.spotLightHelper = new THREE.CameraHelper(spotLight.shadow.camera);
          spotLightHelper.visible = false;
          _this.add(spotLight);
          _this.add(spotLightHelper);
        }

        //Stats
        if (parameters.stats == true) {
          var stats = _this.stats = new Stats();
          stats.domElement.style.position = 'absolute';
          stats.domElement.style.top = '60px';
          _this.canvas.parent().append(jQuery(stats.domElement));
        }
        _this.enableSoftShadow(false);
        _this.animateCount = 0;
        _this.renderCount = 0;

        //_this.composer = new THREE.EffectComposer( _this.renderer );
        //_this.composer.addPass( new THREE.RenderPass( _this, _this.camera ) );
        //
        //_this.pass = new THREE.SMAAPass( window.innerWidth, window.innerHeight );
        //_this.pass.renderToScreen = true;
        //_this.composer.addPass( _this.pass );

        _this.camera.position.set(-300, 300, 300);
        _this.camera.lookAt(new THREE.Vector3(0, 0, 0));
        _this.orbitControl.center.set(0, 0, 0);
        _this.orbitControl.update();
        _this.selectiveRendering = false;
        _this.renderRequestPending = false;
        animate();
      }

      this.type = "Stage";
      _this.cancelRAF = function () {
        cancelAnimationFrame(animate);
        animate = null;
      };

      function animate() {
        if (animate)
          requestAnimationFrame(animate);

        //controls.update();
        if (_this.selectiveRendering != true || (_this.selectiveRendering == true && _this.renderRequestPending == true)) {
          _this.render();

          //$(".quick-hint").html(_this.animateCount);
        }
      }
    }

    Stage.prototype.enableSoftShadow = function (enable) {
      if (this.spotLight.shadow == null) return;

      enable = enable != null ? enable : false;

      var oldType = this.renderer.shadowMap.type;
      this.renderer.shadowMap.type = THREE.PCFShadowMap;//enable ? THREE.PCFSoftShadowMap : THREE.PCFShadowMap;
      var spotLight = this.spotLight;

      if (spotLight.shadow.map) {
        if (oldType == this.renderer.shadowMap.type) return;
        spotLight.shadow.map.dispose();
        spotLight.shadow.map = null;
      }
      var size = enable ? 4096 : 1024;

      spotLight.shadow.mapSize.width = size;
      spotLight.shadow.mapSize.height = size;
      spotLight.shadow.bias = enable ? -0.00000875 : -0.00005;//0.7 * -0.0001 * 4096 / (size * 8);

      this.clearMaterials();
      this.renderRequestPending = true;
    };

    Stage.prototype.clearMaterials = function () {
      var totalChild = this.children.length;
      for (var count = totalChild - 1; count >= 0; count--) {
        var child = this.children[count];
        if (child.baseType && child.baseType == "Paper") {
          if (child.material) {
            if (child.material.materials != null) {
              for (var countMat = 0; countMat < child.material.materials.length; countMat++)
                child.material.materials[countMat].needsUpdate = true;
            }
            else {
              child.material.needsUpdate = true;
            }

          }
        }
      }
    };

    Stage.prototype.clearChild = function () {

      if (this.spotLight.shadow.map) {
        this.spotLight.shadow.map.dispose();
        this.spotLight.shadow.map = null;
      }
      this.spotLight.castShadow = false;
      this.clearMaterials();

      var totalChild = this.children.length;
      for (var count = totalChild - 1; count >= 0; count--) {
        var child = this.children[count];

        if (child instanceof MOCKUP.Bundle) {
          for (var stackCount = child.children.length - 1; stackCount >= 0; stackCount--) {
            MOCKUP.clearChild(child.children[stackCount]);
          }
        }
        MOCKUP.clearChild(child);
        child = null;
      }

      this.render();
    };

    Stage.prototype.resizeAuto = function () {
      this.resizeCanvas(this.canvas.parent().width(), this.canvas.parent().height());
    };

    Stage.prototype.resizeCanvas = function (width, height) {
      this.renderer.setSize(width, height);
      this.camera.aspect = width / height;
      this.camera.updateProjectionMatrix();

      //var pixelRatio = this.renderer.getPixelRatio();
      //var newWidth  = Math.floor( width / pixelRatio ) || 1;
      //var newHeight = Math.floor( height / pixelRatio ) || 1;
      //this.composer.setSize( newWidth, newHeight );
      //this.pass.setSize( newWidth, newHeight );
      this.renderRequestPending = true;
      if (this.resizeCallback != null) this.resizeCallback();
    };

    Stage.prototype.render = function () {
      this.animateCount++;
      //if (this.renderer != null) {
      this.renderer.render(this, this.camera);
      //this.composer.render();

      // option to include any update that maye be required from the parent object or stage
      // like editors and plugins
      //if (this.postRender != null)
      //    this.postRender();
      if (this.stats != null) this.stats.update();
      //this.renderCount++;
      //debugging option for shadowmap
      //if (this.showShadowMap)this.dirLightShadowMapViewer.render(this.renderer);
      //}
      //else {
      //    1 == 1;
      //}
      this.renderRequestPending = false;
      if (this.renderCallback != null) this.renderCallback();

    };

    Stage.prototype.toJSON = function () {

      var output = {
        metadata: {
          version: 4.3,
          type: 'Object',
          generator: 'ObjectExporter'
        }
      };

      //

      var geometries = {};

      var parseGeometry = function (geometry) {

        if (output.geometries == null) {

          output.geometries = [];

        }

        if (geometries[geometry.uuid] == null) {

          var json = geometry.toJSON();

          delete json.metadata;

          geometries[geometry.uuid] = json;

          output.geometries.push(json);

        }

        return geometry.uuid;

      };

      //

      var materials = {};

      var parseMaterial = function (material) {

        if (output.materials == null) {

          output.materials = [];

        }

        if (materials[material.uuid] == null) {

          var json = material.toJSON();

          delete json.metadata;

          materials[material.uuid] = json;

          output.materials.push(json);

        }

        return material.uuid;

      };

      //

      var parseObject = function (object) {

        var data = {};

        data.uuid = object.uuid;
        data.type = object.type;

        if (object.name !== '') data.name = object.name;
        if (JSON.stringify(object.userData) !== '{}') data.userData = object.userData;
        if (object.visible !== true) data.visible = object.visible;

        /*
         geometryType: MOCKUP.GEOMETRY_TYPE.PLANE,
         width: 210,
         height: 297,
         depth: 0.2,
         segments: 150,
         folds: 1,
         angles: [],
         backImage: null,
         frontImage: null,
         frontBump: null,
         backBump: null,
         shininess: 30,
         bumpScale: 0.4,
         stiffness: 0.02,
         color: 0xffffff,
         skipMaterials: false
         heightScale*/

        MOCKUP.getParameter(object, data);

        if (object instanceof THREE.PerspectiveCamera) {

          data.fov = object.fov;
          data.aspect = object.aspect;
          data.near = object.near;
          data.far = object.far;

        } else if (object instanceof THREE.OrthographicCamera) {

          data.left = object.left;
          data.right = object.right;
          data.top = object.top;
          data.bottom = object.bottom;
          data.near = object.near;
          data.far = object.far;

        } else if (object instanceof THREE.AmbientLight) {

          data.color = object.color.getHex();

        } else if (object instanceof THREE.DirectionalLight) {

          data.color = object.color.getHex();
          data.intensity = object.intensity;

        } else if (object instanceof THREE.PointLight) {

          data.color = object.color.getHex();
          data.intensity = object.intensity;
          data.distance = object.distance;
          data.decay = object.decay;

        } else if (object instanceof THREE.SpotLight) {
          data.radius = object.radius;
          data.color = object.color.getHex();
          data.intensity = object.intensity;
          data.distance = object.distance;
          data.angle = object.angle;
          data.exponent = object.exponent;
          data.decay = object.decay;

        } else if (object instanceof THREE.Mesh || object instanceof THREE.Line || object instanceof THREE.PointCloud) {
          if (object instanceof MOCKUP.Iphone) {
          }
          else {
            data.geometry = parseGeometry(object.geometry);
            data.material = parseMaterial(object.material);

            if (object instanceof THREE.Line) data.mode = object.mode;
          }

        } else if (object instanceof THREE.Sprite) {

          data.material = parseMaterial(object.material);

        }

        data.matrix = object.matrix.toArray();

        if (object.children.length > 0) {

          data.children = [];

          for (var i = 0; i < object.children.length; i++) {

            var child = object.children[i];
            if (child instanceof THREE.TransformControls || child instanceof THREE.BoxHelper) {
              //these objects are to be skipped
            }
            else {
              if (!(object instanceof MOCKUP.Bundle))
                data.children.push(parseObject(object.children[i]));
            }


          }

        }

        return data;

      };

      output.object = parseObject(this);

      return output;

    };

    Stage.prototype.hasChild = function () {
      var children = this.children;
      var hasChild = false;
      for (var childCount = 0; childCount < this.children.length; childCount++) {
        if (children[childCount].baseType == "Paper" && children[childCount].type !== "Ground") {
          hasChild = true;
          break;
        }
      }
      return hasChild;
    };
    return Stage;
  })(THREE.Scene);

  MOCKUP.getParameter = function (object, parameters) {

    if (object.subType != null) parameters.subType = object.subType;
    if (object.height != null) parameters.height = object.height;
    if (object.width != null) parameters.width = object.width;
    if (object.depth != null) parameters.depth = object.depth;
    if (object.radius != null) parameters.radius = object.radius; //spotlight
    if (object.segments != null) parameters.segments = object.segments;
    if (object.folds != null) parameters.folds = object.folds;
    if (object.angles != null) parameters.angles = object.angles;
    if (object.shininess != null) parameters.shininess = object.shininess();
    if (object.bumpScale != null) parameters.bumpScale = object.bumpScale();
    if (object.stiffness != null) parameters.stiffness = object.stiffness;
    if (object.heightScale != null) parameters.heightScale = object.heightScale;
    if (object.widthScale != null) parameters.widthScale = object.widthScale;
    if (object.frontImage != null) parameters.frontImage = object.frontImage();
    if (object.backImage != null) parameters.backImage = object.backImage();
    if (object.backBump != null) parameters.backBump = object.backBump();
    if (object.frontBump != null) parameters.frontBump = object.frontBump();
    if (object.cloneParent != null)
      parameters.cloneParent = object.cloneParent;

    // for MOCKUP.Ground
    if (object.textureRepeat != null) parameters.textureRepeat = object.textureRepeat();

    //for MOCKUP.UI
    if (object.resolutionHeight != null) parameters.resolutionHeight = object.resolutionHeight;
    if (object.resolutionWidth != null) parameters.resolutionWidth = object.resolutionWidth;
    if (object.screenSize != null) parameters.screenSize = object.screenSize;

    if (MOCKUP.overrideAsTemplate == true) {
      MOCKUP.getDefaultImage(parameters);
      if (parameters.defaultImage != null) {
        if (object.frontImage != null) parameters.frontImage = parameters.defaultImage[4];
        if (object.backImage != null) parameters.backImage = parameters.defaultImage[5];
      }
    }
  };

  MOCKUP.getDefaultImage = function (parameters) {
    if (parameters.defaultImage == null
      && MOCKUP.presets[parameters.type] != null
      && MOCKUP.presets[parameters.type].options != null) {

      parameters.defaultImage = MOCKUP.presets[parameters.type].options.defaultImage;

      //see if subType value exists
      if (parameters.subType != null
        && MOCKUP.presets[parameters.type].menu != null
        && MOCKUP.presets[parameters.type].menu.subMenu != null
        && MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType] != null
        && MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType].options.defaultImage != null
      ) {
        parameters.defaultImage = MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType].options.defaultImage;
      }
    }
  };

  MOCKUP.createObject = function (parameters) {
    if (parameters.defaultImage == null
      && MOCKUP.presets != null
      && MOCKUP.presets[parameters.type] != null
      && MOCKUP.presets[parameters.type].options != null) {

      parameters.defaultImage = MOCKUP.presets[parameters.type].options.defaultImage;

      //see if subType value exists
      if (parameters.subType != null
        && MOCKUP.presets[parameters.type].menu != null
        && MOCKUP.presets[parameters.type].menu.subMenu != null
        && MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType] != null
        && MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType].options.defaultImage != null
      ) {
        parameters.defaultImage = MOCKUP.presets[parameters.type].menu.subMenu[parameters.subType].options.defaultImage;
      }
    }
    var object = new MOCKUP[parameters.type](parameters);
    if (parameters.cloneParent != null) object.cloneParent = parameters.cloneParent;
    return object;
  };

})(MOCKUP || (MOCKUP = {}));

//Paper derivatives
(function (MOCKUP) {

  var PlanePaper = (function (_super) {
    MOCKUP.__extends(PlanePaper, _super);
    function PlanePaper(parameters, stage) {
      parameters = parameters || {};
      parameters.geometryType = MOCKUP.GEOMETRY_TYPE.PLANE;
      _super.call(this, parameters, stage);
      this.type = "PlanePaper";
    }

    PlanePaper.prototype.backImage = null;
    PlanePaper.prototype.backBump = null;
    return PlanePaper;
  })(MOCKUP.Paper);
  MOCKUP.PlanePaper = PlanePaper;

  /**
   * BoxPaper variation will have depth feature
   */
  var BoxPaper = (function (_super) {
    MOCKUP.__extends(BoxPaper, _super);
    function BoxPaper(parameters, stage) {
      parameters = parameters || {};
      parameters.geometryType = MOCKUP.GEOMETRY_TYPE.BOX;
      _super.call(this, parameters, stage);
      this.type = "BoxPaper";
      this.castShadow = true;
      this.receiveShadow = true;
    }

    BoxPaper.prototype.updateGeometry = function () {
      //console.log("updated box paper");
    };
    return BoxPaper
  })(MOCKUP.Paper);
  ;
  MOCKUP.BoxPaper = BoxPaper;

  var FlexBoxPaper = (function (_super) {
    MOCKUP.__extends(FlexBoxPaper, _super);
    function FlexBoxPaper(parameters, stage) {
      parameters = parameters || {};

      _super.call(this, parameters, stage);

      var angles = 6; //temporarily serving till trifold
      for (var _setAngle = 0; _setAngle < angles; _setAngle++) {
        this.angles[_setAngle] = this.angles[_setAngle] != null ? this.angles[_setAngle] : 0;
      }
      //First and last are flexible points unless its a single fold

      //Update: not designed for single fold
      //if (this.folds > 1) {
      //    this.angles[0] = 0;
      //    this.angles[this.angles.length - 1] = 0;
      //}

      //this.add(this.body);
      this.updateAngle();

      this.type = "FoldBoxPaper";
    }

    FlexBoxPaper.prototype.updateGeometry = function () {
      //this.createGeometry();
      //this.updateAngle();
    };

    FlexBoxPaper.prototype.updateAngle = function (skipNormals) {
      //console.log("called");
      var _this = this;
      var start = performance.now();
      var width = _this.width * (1 - Math.sin((_this.stiffness / 2) * (_this.stiffness / 2)) / 2) - _this.width * (_this.stiffness) / 20;
      var height = _this.height;

      var segments = _this.segments;
      var foldcount = _this.folds;
      var stiffness = _this.stiffness;


      var hw = width / 2;
      var hh = height / 2;
      var fw = width / foldcount; //fold-width
      var bw = fw * stiffness; //bend control pint distance
      var cw = fw; //curve width still not perfect
      //control pointF are a list of at least (degree+1)
      var curvesF = [];
      var curvesB = [];
      var verticesF = [];
      var verticesB = [];
      var pointF = [];
      var pointB = [];
      var _angle = 0;
      var db = _this.depth; // distance Bias
      var _90 = Math.PI / 2;
      var langle = _this.angles[4] || 0;
      var trifoldDisplace = foldcount == 3 ? cw / 200 : 0;
      var sAngle = 0; //shift angle - reference angle for the front and back side
      //if (_this.folds> 2) cw = 0.95 * (fw );
      //calculate folds controls points
      var sum = 0, distances = [];
      distances.push(sum);
      var test = false;

      function calcPoints() {
        if (_this.folds >= 1) {
          pointF[0] = [];
          pointB[foldcount - 1] = [];

          _angle = _this.angles[1] * Math.PI / 180;
          langle = (( _this.angles[4])) * Math.PI / 180;
          var raisedHeight = Math.sin(langle) * cw;
          pointF[0][0] = pointF[0][1] = (_this.folds == 1)
            ? new THREE.Vector3(-cw * Math.cos(_angle), 0, Math.sin(_angle) * cw)
            : new THREE.Vector3(cw - hw - cw * Math.cos(_angle), 0, Math.sin(_angle) * cw);

          sAngle = ((_this.angles[1] - 90) * Math.PI / 180);
          _angle = sAngle;
          pointB[foldcount - 1][2] = pointB[foldcount - 1][3] = new THREE.Vector3(pointF[0][0].x - Math.cos(_angle) * db, 0, pointF[0][0].z + Math.sin(_angle) * db); //new THREE.Vector3(cw * 2 - hw - (Math.cos(_angle + Math.PI - aB) * cw), 0, Math.sin(_angle + Math.PI - aB) * cw);


          /*lift Angle Scenario only in single folds and book.*/
          if (_this.folds == 1) {
            //var lw = _langle = (( 0+ _this.angles[4])/4) * Math.PI / 180;
            pointF[0][1] = new THREE.Vector3(-cw / 2 * Math.cos(langle), 0, cw / 2 * Math.sin(langle));
            pointB[foldcount - 1][2] = new THREE.Vector3(pointF[0][1].x - Math.cos(sAngle) * db, 0, pointF[0][1].z + Math.sin(sAngle) * db);

          }


          _angle = (45 + _this.angles[1] / 2) * Math.PI / 180;
          if (_this.folds > 1) {
            if (_this.folds == 2) {

              _angle = (45 + _this.angles[1] / 4 - (_this.angles[4]) / 2 ) * Math.PI / 180;
            }
            else
              _angle = (45 + _this.angles[1] / 4) * Math.PI / 180;
          }

          pointF[0][2] = (_this.folds == 1)
            ? new THREE.Vector3(-Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw)
            : new THREE.Vector3(cw - hw - trifoldDisplace - Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw);

          pointB[foldcount - 1][1] = (_this.folds == 1)
            ? new THREE.Vector3(pointF[0][2].x - Math.cos(sAngle) * db, 0, pointF[0][2].z + Math.sin(sAngle) * db)
            : pointF[0][2];

          pointF[0][3] = (_this.folds == 1)
            ? new THREE.Vector3(0, 0, 0)
            : new THREE.Vector3(cw - hw - trifoldDisplace, 0, 0);

          if (_this.folds == 2) {

            _angle = (_this.angles[1] / 2 - (_this.angles[4]) / 2 - 90 ) * Math.PI / 180;
          }
          else if (_this.folds == 1) {
            _angle = ((_this.angles[1] - 90) * Math.PI / 180);
          }
          else {
            _angle = ((_this.angles[1] / 2 - 90 ) * Math.PI / 180);
          }

          //console.log(_angle);
          pointB[foldcount - 1][0] = new THREE.Vector3(pointF[0][3].x - Math.cos(_angle) * db, 0, pointF[0][3].z + Math.sin(_angle) * db); //pointF[0][3];

        }
        //if (_this.folds> 2) cw = fw;
        if (_this.folds >= 2) {
          pointF[1] = [];
          pointB[foldcount - 2] = [];

          pointF[1][0] = pointF[0][3];
          pointB[foldcount - 2][3] = pointB[foldcount - 1][0];

          _angle = (135 + _this.angles[1] * 3 / 4) * Math.PI / 180;
          pointF[1][1] = new THREE.Vector3(cw - hw - Math.cos(_angle) * bw, 0, Math.sin(_angle) * bw);
          pointB[foldcount - 2][2] = pointF[1][1];

          _angle = 0;
          if (_this.folds > 2) {
            _angle = (45 - _this.angles[4] * 3 / 4) * Math.PI / 180;

            pointF[1][2] = new THREE.Vector3(cw * 2 - hw + trifoldDisplace - (Math.cos(_angle) * bw), 0, Math.sin(_angle) * bw);
            pointB[foldcount - 2][1] = pointF[1][2];

            pointF[1][3] = new THREE.Vector3(cw * 2 - hw + trifoldDisplace, 0, 0);
            _angle = ((_this.angles[4] / 2 - 90) * Math.PI / 180);
            pointB[foldcount - 2][0] = new THREE.Vector3(pointF[1][3].x + Math.cos(_angle) * db * 1.25, 0, pointF[1][3].z + Math.sin(_angle) * db * 1.25); //pointF[1][3];
          }
          else {
            _angle = (135 - _this.angles[4] / 4 + _this.angles[1] / 2) * Math.PI / 180;
            pointF[1][1] = new THREE.Vector3(cw - hw - Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw);
            pointB[0][2] = pointF[1][1];

            _angle = (180 - _this.angles[4]) * Math.PI / 180;
            pointF[1][2] = pointF[1][3] = new THREE.Vector3(cw - hw - (Math.cos(_angle) * cw), 0, Math.sin(_angle) * cw);
            _angle = ((_this.angles[4] - 90) * Math.PI / 180);
            //console.log(_angle * 180 / Math.PI);
            pointB[0][0] = pointB[0][1] = new THREE.Vector3(pointF[1][3].x + Math.cos(_angle) * db, 0, pointF[1][3].z + Math.sin(_angle) * db); //pointF[2][2];
          }
        }
        //if (_this.folds> 2) cw =(fw)/0.95;
        if (_this.folds > 2) {
          pointF[2] = [];
          pointB[0] = [];

          pointF[2][0] = pointF[1][3];
          pointB[0][3] = pointB[1][0];

          _angle = (135 - _this.angles[4] * 1 / 4) * Math.PI / 180;
          pointF[2][1] = new THREE.Vector3(cw * 2 - hw - Math.cos(_angle) * 0, 0, Math.sin(_angle) * bw);
          pointB[0][2] = pointF[2][1];

          _angle = (180 - _this.angles[4]) * Math.PI / 180;
          pointF[2][2] = pointF[2][3] = new THREE.Vector3(cw * 2 - hw - (Math.cos(_angle) * cw), 0, Math.sin(_angle) * cw);
          _angle = ((_this.angles[4] - 90) * Math.PI / 180);
          //console.log(_angle * 180 / Math.PI);
          pointB[0][0] = pointB[0][1] = new THREE.Vector3(pointF[2][3].x + Math.cos(_angle) * db, 0, pointF[2][3].z + Math.sin(_angle) * db); //pointF[2][2];
        }

        for (var curveCount = 0; curveCount < _this.folds; curveCount++) {
          curvesF[curveCount] = new THREE.CubicBezierCurve3(
            pointF[curveCount][0], pointF[curveCount][1], pointF[curveCount][2], pointF[curveCount][3]);
          verticesF[curveCount] = curvesF[curveCount].getPoints(_this.segments);
          var current, last = verticesF[curveCount][0];
          for (var vcount = 1; vcount < verticesF[curveCount].length; vcount++) {
            current = verticesF[curveCount][vcount];
            sum += current.distanceTo(last);
            distances.push(sum);
            last = current;
          }

          //if (test !== true) {
          //if(_this.folds==2)console.log(_this.stiffness,_this.width/_this.folds,width/_this.folds,curvesF[curveCount].getLength());
          curvesB[curveCount] = new THREE.CubicBezierCurve3(
            pointB[curveCount][0], pointB[curveCount][1], pointB[curveCount][2], pointB[curveCount][3]);
          verticesB[curveCount] = curvesB[curveCount].getPoints(_this.segments);
          //console.log(curvesB[curveCount].getLength());
          //}
        }
        /*if(test!==true ) {
         if (_this.handles == null) {

         var material = new THREE.LineBasicMaterial({color: 0x0000ff});

         var geometry = new THREE.Geometry();
         geometry.vertices.push(
         pointF[0][0], pointF[0][1], pointF[0][2], pointF[0][3]
         );

         _this.handles = new THREE.Line(geometry, material);
         _this.add(_this.handles);

         } else {
         var hvs = _this.handles.geometry.vertices;
         for (var hcount = 0; hcount < hvs.length; hcount++) {
         hvs[hcount] = pointF[0][hcount];
         }
         _this.handles.geometry.verticesNeedUpdate = true;
         }
         }*/
      }

      //window.points = [pointF, pointB]; //just for debugging
      calcPoints();
      var initialSum = sum;


      //console.log(distances);
      var bodyG = _this.geometry;

      var rowVertices = foldcount * segments + 1;
      var initialOffset = 7, offset = 8;

      //Update the geometry based on angles
      bodyG.vertices[0].z = bodyG.vertices[2].z = bodyG.vertices[offset + rowVertices * 2 - 1].z = bodyG.vertices[offset + rowVertices * 3 - 1].z = bodyG.vertices[offset + rowVertices * 5 - 1].z = bodyG.vertices[offset + rowVertices * 6 - 1].z = verticesF[foldcount - 1][segments].z;
      bodyG.vertices[0].x = bodyG.vertices[2].x = bodyG.vertices[offset + rowVertices * 2 - 1].x = bodyG.vertices[offset + rowVertices * 3 - 1].x = bodyG.vertices[offset + rowVertices * 5 - 1].x = bodyG.vertices[offset + rowVertices * 6 - 1].x = verticesF[foldcount - 1][segments].x;

      bodyG.vertices[1].z = bodyG.vertices[3].z = bodyG.vertices[offset + rowVertices - 1].z = bodyG.vertices[offset + rowVertices * 4 - 1].z = bodyG.vertices[offset + rowVertices * 6].z = bodyG.vertices[offset + rowVertices * 7].z = verticesB[0][0].z;
      bodyG.vertices[1].x = bodyG.vertices[3].x = bodyG.vertices[offset + rowVertices - 1].x = bodyG.vertices[offset + rowVertices * 4 - 1].x = bodyG.vertices[offset + rowVertices * 6].x = bodyG.vertices[offset + rowVertices * 7].x = verticesB[0][0].x;

      bodyG.vertices[5].z = bodyG.vertices[7].z = bodyG.vertices[offset + rowVertices].z = bodyG.vertices[offset + rowVertices * 2].z = bodyG.vertices[offset + rowVertices * 4].z = bodyG.vertices[offset + rowVertices * 5].z = verticesF[0][0].z;
      bodyG.vertices[5].x = bodyG.vertices[7].x = bodyG.vertices[offset + rowVertices].x = bodyG.vertices[offset + rowVertices * 2].x = bodyG.vertices[offset + rowVertices * 4].x = bodyG.vertices[offset + rowVertices * 5].x = verticesF[0][0].x;

      bodyG.vertices[4].z = bodyG.vertices[6].z = bodyG.vertices[offset].z = bodyG.vertices[offset + rowVertices * 3].z = bodyG.vertices[offset + rowVertices * 7 - 1].z = bodyG.vertices[offset + rowVertices * 8 - 1].z = verticesB[foldcount - 1][segments].z;
      bodyG.vertices[4].x = bodyG.vertices[6].x = bodyG.vertices[offset].x = bodyG.vertices[offset + rowVertices * 3].x = bodyG.vertices[offset + rowVertices * 7 - 1].x = bodyG.vertices[offset + rowVertices * 8 - 1].x = verticesB[foldcount - 1][segments].x;

      //
      //
      //rowVertices = foldcount * segments-1;
      var totalVertices = bodyG.vertices.length;
      offset++;

      for (var fold = 0; fold < foldcount; fold++) {
        var isEdge = (fold == 0);
        var _vertices = isEdge ? segments - 1 : segments;
        for (var count = 0; count < _vertices; count++) {
          bodyG.vertices[offset].z = bodyG.vertices[offset + rowVertices * 3].z = bodyG.vertices[totalVertices - offset + initialOffset - rowVertices].z = bodyG.vertices[totalVertices - offset + initialOffset].z = verticesB[foldcount - 1 - fold][_vertices - count].z;
          bodyG.vertices[offset].x = bodyG.vertices[offset + rowVertices * 3].x = bodyG.vertices[totalVertices - offset + initialOffset - rowVertices].x = bodyG.vertices[totalVertices - offset + initialOffset].x = verticesB[foldcount - 1 - fold][_vertices - count].x;

          bodyG.vertices[offset + rowVertices].z = bodyG.vertices[offset + rowVertices * 2].z = bodyG.vertices[offset + rowVertices * 4].z = bodyG.vertices[offset + rowVertices * 5].z = verticesF[fold][count + isEdge].z;
          bodyG.vertices[offset + rowVertices].x = bodyG.vertices[offset + rowVertices * 2].x = bodyG.vertices[offset + rowVertices * 4].x = bodyG.vertices[offset + rowVertices * 5].x = verticesF[fold][count + isEdge].x;

          offset++;
        }
      }

      var uvs = bodyG.faceVertexUvs[0];
      var faces = bodyG.faces;
      var uvindexb = 0, uvindexf = 0;
      for (var count = 0; count < uvs.length; count++) {
        if (faces[count].materialIndex == MOCKUP.MATERIAL_FACE.FRONT) {
          var dist = distances[uvindexf] / sum;
          if ((count % 2) == 0) {
            uvs[count][0].x = uvs[count][1].x = uvs[count + 1][0].x = dist;
            uvindexf++;
          } else {
            uvs[count - 1][2].x = uvs[count][1].x = uvs[count][2].x = dist;
          }
        }
        else if (faces[count].materialIndex == MOCKUP.MATERIAL_FACE.BACK) {
          var dist = 1 - distances[uvindexf] / sum;
          //console.log(dist);
          if ((count % 2) == 0) {
            uvs[count][0].x = uvs[count][1].x = uvs[count + 1][0].x = dist;
            uvindexf--;
          } else {
            uvs[count - 1][2].x = uvs[count][1].x = uvs[count][2].x = dist;
          }
        }
      }

      bodyG.computeBoundingBox();
      var boundWidth = Math.abs(bodyG.boundingBox.min.x - bodyG.boundingBox.max.x);
      var boundHeight = Math.abs(bodyG.boundingBox.min.z - bodyG.boundingBox.max.z);
      var boundDiagonal = Math.sqrt(boundWidth * boundWidth + boundHeight * boundHeight);
      //console.log(cw/sum);
      _this.scale.x = _this.scale.z = cw * _this.folds / sum;


      bodyG.computeBoundingSphere();
      bodyG.verticesNeedUpdate = true;
      bodyG.computeFaceNormals();
      //if(skipNormals !=true) {
      //
      //
      //}
      bodyG.computeVertexNormals();
      bodyG.uvsNeedUpdate = true;
      bodyG.normalsNeedUpdate = true;

      curvesF.forEach(function (curveF) {
        curveF = null;
      });
      curvesB.forEach(function (curveB) {
        curveB = null;
      });
      verticesB.forEach(function (vertexB) {
        vertexB = null;
      });
      verticesF.forEach(function (vertexF) {
        vertexF = null;
      });
      //console.log( "FlexBoxPaper UpdateAngle: " + ( performance.now() - start ).toFixed(2) + 'ms');
      //$(".quick-hint").html("initial" +  Math.round(initialSum) +"  next" +  Math.round(cw) + "   Sum:" + Math.round(sum));
    };

    return FlexBoxPaper;
  })(MOCKUP.BoxPaper);
  MOCKUP.FlexBoxPaper = FlexBoxPaper;

  var FlexBoxPaper = (function (_super) {
    MOCKUP.__extends(FlexBoxPaper, _super);
    function FlexBoxPaper(parameters, stage) {
      parameters = parameters || {};

      _super.call(this, parameters, stage);

      var angles = 6; //temporarily serving till trifold
      for (var _setAngle = 0; _setAngle < angles; _setAngle++) {
        this.angles[_setAngle] = this.angles[_setAngle] != null ? this.angles[_setAngle] : 0;
      }
      //First and last are flexible points unless its a single fold

      //Update: not designed for single fold
      //if (this.folds > 1) {
      //    this.angles[0] = 0;
      //    this.angles[this.angles.length - 1] = 0;
      //}

      //this.add(this.body);
      this.updateAngle();

      this.type = "FoldBoxPaper";
    }

    FlexBoxPaper.prototype.updateGeometry = function () {
      //this.createGeometry();
      //this.updateAngle();
    };

    FlexBoxPaper.prototype.updateAngle = function (skipNormals) {
      //console.log("called");
      var _this = this;
      var start = performance.now();
      var width = _this.width * (1 - Math.sin((_this.stiffness / 2) * (_this.stiffness / 2)) / 2) - _this.width * (_this.stiffness) / 20;
      var height = _this.height;

      var segments = _this.segments;
      var foldcount = _this.folds;
      var stiffness = _this.stiffness;


      var hw = width / 2;
      var hh = height / 2;
      var fw = width / foldcount; //fold-width
      var bw = fw * stiffness; //bend control pint distance
      var cw = fw; //curve width still not perfect
      //control pointF are a list of at least (degree+1)
      var curvesF = [];
      var curvesB = [];
      var verticesF = [];
      var verticesB = [];
      var pointF = [];
      var pointB = [];
      var _angle = 0;
      var db = _this.depth; // distance Bias
      var _90 = Math.PI / 2;
      var langle = _this.angles[4] || 0;
      var trifoldDisplace = foldcount == 3 ? cw / 200 : 0;
      var sAngle = 0; //shift angle - reference angle for the front and back side
      //if (_this.folds> 2) cw = 0.95 * (fw );
      //calculate folds controls points
      var sum = 0, distances = [];
      distances.push(sum);
      var test = false;

      function calcPoints() {
        if (_this.folds >= 1) {
          pointF[0] = [];
          pointB[foldcount - 1] = [];

          _angle = _this.angles[1] * Math.PI / 180;
          langle = (( _this.angles[4])) * Math.PI / 180;
          var raisedHeight = Math.sin(langle) * cw;
          pointF[0][0] = pointF[0][1] = (_this.folds == 1)
            ? new THREE.Vector3(-cw * Math.cos(_angle), 0, Math.sin(_angle) * cw)
            : new THREE.Vector3(cw - hw - cw * Math.cos(_angle), 0, Math.sin(_angle) * cw);

          sAngle = ((_this.angles[1] - 90) * Math.PI / 180);
          _angle = sAngle;
          pointB[foldcount - 1][2] = pointB[foldcount - 1][3] = new THREE.Vector3(pointF[0][0].x - Math.cos(_angle) * db, 0, pointF[0][0].z + Math.sin(_angle) * db); //new THREE.Vector3(cw * 2 - hw - (Math.cos(_angle + Math.PI - aB) * cw), 0, Math.sin(_angle + Math.PI - aB) * cw);


          /*lift Angle Scenario only in single folds and book.*/
          if (_this.folds == 1) {
            //var lw = _langle = (( 0+ _this.angles[4])/4) * Math.PI / 180;
            pointF[0][1] = new THREE.Vector3(-cw / 2 * Math.cos(langle), 0, cw / 2 * Math.sin(langle));
            pointB[foldcount - 1][2] = new THREE.Vector3(pointF[0][1].x - Math.cos(sAngle) * db, 0, pointF[0][1].z + Math.sin(sAngle) * db);

          }


          _angle = (45 + _this.angles[1] / 2) * Math.PI / 180;
          if (_this.folds > 1) {
            if (_this.folds == 2) {

              _angle = (45 + _this.angles[1] / 4 - (_this.angles[4]) / 2 ) * Math.PI / 180;
            }
            else
              _angle = (45 + _this.angles[1] / 4) * Math.PI / 180;
          }

          pointF[0][2] = (_this.folds == 1)
            ? new THREE.Vector3(-Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw)
            : new THREE.Vector3(cw - hw - trifoldDisplace - Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw);

          pointB[foldcount - 1][1] = (_this.folds == 1)
            ? new THREE.Vector3(pointF[0][2].x - Math.cos(sAngle) * db, 0, pointF[0][2].z + Math.sin(sAngle) * db)
            : pointF[0][2];

          pointF[0][3] = (_this.folds == 1)
            ? new THREE.Vector3(0, 0, 0)
            : new THREE.Vector3(cw - hw - trifoldDisplace, 0, 0);

          if (_this.folds == 2) {

            _angle = (_this.angles[1] / 2 - (_this.angles[4]) / 2 - 90 ) * Math.PI / 180;
          }
          else if (_this.folds == 1) {
            _angle = ((_this.angles[1] - 90) * Math.PI / 180);
          }
          else {
            _angle = ((_this.angles[1] / 2 - 90 ) * Math.PI / 180);
          }

          //console.log(_angle);
          pointB[foldcount - 1][0] = new THREE.Vector3(pointF[0][3].x - Math.cos(_angle) * db, 0, pointF[0][3].z + Math.sin(_angle) * db); //pointF[0][3];

        }
        //if (_this.folds> 2) cw = fw;
        if (_this.folds >= 2) {
          pointF[1] = [];
          pointB[foldcount - 2] = [];

          pointF[1][0] = pointF[0][3];
          pointB[foldcount - 2][3] = pointB[foldcount - 1][0];

          _angle = (135 + _this.angles[1] * 3 / 4) * Math.PI / 180;
          pointF[1][1] = new THREE.Vector3(cw - hw - Math.cos(_angle) * bw, 0, Math.sin(_angle) * bw);
          pointB[foldcount - 2][2] = pointF[1][1];

          _angle = 0;
          if (_this.folds > 2) {
            _angle = (45 - _this.angles[4] * 3 / 4) * Math.PI / 180;

            pointF[1][2] = new THREE.Vector3(cw * 2 - hw + trifoldDisplace - (Math.cos(_angle) * bw), 0, Math.sin(_angle) * bw);
            pointB[foldcount - 2][1] = pointF[1][2];

            pointF[1][3] = new THREE.Vector3(cw * 2 - hw + trifoldDisplace, 0, 0);
            _angle = ((_this.angles[4] / 2 - 90) * Math.PI / 180);
            pointB[foldcount - 2][0] = new THREE.Vector3(pointF[1][3].x + Math.cos(_angle) * db * 1.25, 0, pointF[1][3].z + Math.sin(_angle) * db * 1.25); //pointF[1][3];
          }
          else {
            _angle = (135 - _this.angles[4] / 4 + _this.angles[1] / 2) * Math.PI / 180;
            pointF[1][1] = new THREE.Vector3(cw - hw - Math.cos(_angle) * bw / 2, 0, Math.sin(_angle) * bw);
            pointB[0][2] = pointF[1][1];

            _angle = (180 - _this.angles[4]) * Math.PI / 180;
            pointF[1][2] = pointF[1][3] = new THREE.Vector3(cw - hw - (Math.cos(_angle) * cw), 0, Math.sin(_angle) * cw);
            _angle = ((_this.angles[4] - 90) * Math.PI / 180);
            //console.log(_angle * 180 / Math.PI);
            pointB[0][0] = pointB[0][1] = new THREE.Vector3(pointF[1][3].x + Math.cos(_angle) * db, 0, pointF[1][3].z + Math.sin(_angle) * db); //pointF[2][2];
          }
        }
        //if (_this.folds> 2) cw =(fw)/0.95;
        if (_this.folds > 2) {
          pointF[2] = [];
          pointB[0] = [];

          pointF[2][0] = pointF[1][3];
          pointB[0][3] = pointB[1][0];

          _angle = (135 - _this.angles[4] * 1 / 4) * Math.PI / 180;
          pointF[2][1] = new THREE.Vector3(cw * 2 - hw - Math.cos(_angle) * 0, 0, Math.sin(_angle) * bw);
          pointB[0][2] = pointF[2][1];

          _angle = (180 - _this.angles[4]) * Math.PI / 180;
          pointF[2][2] = pointF[2][3] = new THREE.Vector3(cw * 2 - hw - (Math.cos(_angle) * cw), 0, Math.sin(_angle) * cw);
          _angle = ((_this.angles[4] - 90) * Math.PI / 180);
          //console.log(_angle * 180 / Math.PI);
          pointB[0][0] = pointB[0][1] = new THREE.Vector3(pointF[2][3].x + Math.cos(_angle) * db, 0, pointF[2][3].z + Math.sin(_angle) * db); //pointF[2][2];
        }

        for (var curveCount = 0; curveCount < _this.folds; curveCount++) {
          curvesF[curveCount] = new THREE.CubicBezierCurve3(
            pointF[curveCount][0], pointF[curveCount][1], pointF[curveCount][2], pointF[curveCount][3]);
          verticesF[curveCount] = curvesF[curveCount].getPoints(_this.segments);
          var current, last = verticesF[curveCount][0];
          for (var vcount = 1; vcount < verticesF[curveCount].length; vcount++) {
            current = verticesF[curveCount][vcount];
            sum += current.distanceTo(last);
            distances.push(sum);
            last = current;
          }

          //if (test !== true) {
          //if(_this.folds==2)console.log(_this.stiffness,_this.width/_this.folds,width/_this.folds,curvesF[curveCount].getLength());
          curvesB[curveCount] = new THREE.CubicBezierCurve3(
            pointB[curveCount][0], pointB[curveCount][1], pointB[curveCount][2], pointB[curveCount][3]);
          verticesB[curveCount] = curvesB[curveCount].getPoints(_this.segments);
          //console.log(curvesB[curveCount].getLength());
          //}
        }
        /*if(test!==true ) {
         if (_this.handles == null) {

         var material = new THREE.LineBasicMaterial({color: 0x0000ff});

         var geometry = new THREE.Geometry();
         geometry.vertices.push(
         pointF[0][0], pointF[0][1], pointF[0][2], pointF[0][3]
         );

         _this.handles = new THREE.Line(geometry, material);
         _this.add(_this.handles);

         } else {
         var hvs = _this.handles.geometry.vertices;
         for (var hcount = 0; hcount < hvs.length; hcount++) {
         hvs[hcount] = pointF[0][hcount];
         }
         _this.handles.geometry.verticesNeedUpdate = true;
         }
         }*/
      }

      //window.points = [pointF, pointB]; //just for debugging
      calcPoints();
      var initialSum = sum;


      //console.log(distances);
      var bodyG = _this.geometry;

      var rowVertices = foldcount * segments + 1;
      var initialOffset = 7, offset = 8;

      //Update the geometry based on angles
      bodyG.vertices[0].z = bodyG.vertices[2].z = bodyG.vertices[offset + rowVertices * 2 - 1].z = bodyG.vertices[offset + rowVertices * 3 - 1].z = bodyG.vertices[offset + rowVertices * 5 - 1].z = bodyG.vertices[offset + rowVertices * 6 - 1].z = verticesF[foldcount - 1][segments].z;
      bodyG.vertices[0].x = bodyG.vertices[2].x = bodyG.vertices[offset + rowVertices * 2 - 1].x = bodyG.vertices[offset + rowVertices * 3 - 1].x = bodyG.vertices[offset + rowVertices * 5 - 1].x = bodyG.vertices[offset + rowVertices * 6 - 1].x = verticesF[foldcount - 1][segments].x;

      bodyG.vertices[1].z = bodyG.vertices[3].z = bodyG.vertices[offset + rowVertices - 1].z = bodyG.vertices[offset + rowVertices * 4 - 1].z = bodyG.vertices[offset + rowVertices * 6].z = bodyG.vertices[offset + rowVertices * 7].z = verticesB[0][0].z;
      bodyG.vertices[1].x = bodyG.vertices[3].x = bodyG.vertices[offset + rowVertices - 1].x = bodyG.vertices[offset + rowVertices * 4 - 1].x = bodyG.vertices[offset + rowVertices * 6].x = bodyG.vertices[offset + rowVertices * 7].x = verticesB[0][0].x;

      bodyG.vertices[5].z = bodyG.vertices[7].z = bodyG.vertices[offset + rowVertices].z = bodyG.vertices[offset + rowVertices * 2].z = bodyG.vertices[offset + rowVertices * 4].z = bodyG.vertices[offset + rowVertices * 5].z = verticesF[0][0].z;
      bodyG.vertices[5].x = bodyG.vertices[7].x = bodyG.vertices[offset + rowVertices].x = bodyG.vertices[offset + rowVertices * 2].x = bodyG.vertices[offset + rowVertices * 4].x = bodyG.vertices[offset + rowVertices * 5].x = verticesF[0][0].x;

      bodyG.vertices[4].z = bodyG.vertices[6].z = bodyG.vertices[offset].z = bodyG.vertices[offset + rowVertices * 3].z = bodyG.vertices[offset + rowVertices * 7 - 1].z = bodyG.vertices[offset + rowVertices * 8 - 1].z = verticesB[foldcount - 1][segments].z;
      bodyG.vertices[4].x = bodyG.vertices[6].x = bodyG.vertices[offset].x = bodyG.vertices[offset + rowVertices * 3].x = bodyG.vertices[offset + rowVertices * 7 - 1].x = bodyG.vertices[offset + rowVertices * 8 - 1].x = verticesB[foldcount - 1][segments].x;

      //
      //
      //rowVertices = foldcount * segments-1;
      var totalVertices = bodyG.vertices.length;
      offset++;

      for (var fold = 0; fold < foldcount; fold++) {
        var isEdge = (fold == 0);
        var _vertices = isEdge ? segments - 1 : segments;
        for (var count = 0; count < _vertices; count++) {
          bodyG.vertices[offset].z = bodyG.vertices[offset + rowVertices * 3].z = bodyG.vertices[totalVertices - offset + initialOffset - rowVertices].z = bodyG.vertices[totalVertices - offset + initialOffset].z = verticesB[foldcount - 1 - fold][_vertices - count].z;
          bodyG.vertices[offset].x = bodyG.vertices[offset + rowVertices * 3].x = bodyG.vertices[totalVertices - offset + initialOffset - rowVertices].x = bodyG.vertices[totalVertices - offset + initialOffset].x = verticesB[foldcount - 1 - fold][_vertices - count].x;

          bodyG.vertices[offset + rowVertices].z = bodyG.vertices[offset + rowVertices * 2].z = bodyG.vertices[offset + rowVertices * 4].z = bodyG.vertices[offset + rowVertices * 5].z = verticesF[fold][count + isEdge].z;
          bodyG.vertices[offset + rowVertices].x = bodyG.vertices[offset + rowVertices * 2].x = bodyG.vertices[offset + rowVertices * 4].x = bodyG.vertices[offset + rowVertices * 5].x = verticesF[fold][count + isEdge].x;

          offset++;
        }
      }

      var uvs = bodyG.faceVertexUvs[0];
      var faces = bodyG.faces;
      var uvindexb = 0, uvindexf = 0;
      for (var count = 0; count < uvs.length; count++) {
        if (faces[count].materialIndex == MOCKUP.MATERIAL_FACE.FRONT) {
          var dist = distances[uvindexf] / sum;
          if ((count % 2) == 0) {
            uvs[count][0].x = uvs[count][1].x = uvs[count + 1][0].x = dist;
            uvindexf++;
          } else {
            uvs[count - 1][2].x = uvs[count][1].x = uvs[count][2].x = dist;
          }
        }
        else if (faces[count].materialIndex == MOCKUP.MATERIAL_FACE.BACK) {
          var dist = 1 - distances[uvindexf] / sum;
          //console.log(dist);
          if ((count % 2) == 0) {
            uvs[count][0].x = uvs[count][1].x = uvs[count + 1][0].x = dist;
            uvindexf--;
          } else {
            uvs[count - 1][2].x = uvs[count][1].x = uvs[count][2].x = dist;
          }
        }
      }

      bodyG.computeBoundingBox();
      var boundWidth = Math.abs(bodyG.boundingBox.min.x - bodyG.boundingBox.max.x);
      var boundHeight = Math.abs(bodyG.boundingBox.min.z - bodyG.boundingBox.max.z);
      var boundDiagonal = Math.sqrt(boundWidth * boundWidth + boundHeight * boundHeight);
      //console.log(cw/sum);
      _this.scale.x = _this.scale.z = cw * _this.folds / sum;


      bodyG.computeBoundingSphere();
      bodyG.verticesNeedUpdate = true;
      bodyG.computeFaceNormals();
      //if(skipNormals !=true) {
      //
      //
      //}
      bodyG.computeVertexNormals();
      bodyG.uvsNeedUpdate = true;
      bodyG.normalsNeedUpdate = true;

      curvesF.forEach(function (curveF) {
        curveF = null;
      });
      curvesB.forEach(function (curveB) {
        curveB = null;
      });
      verticesB.forEach(function (vertexB) {
        vertexB = null;
      });
      verticesF.forEach(function (vertexF) {
        vertexF = null;
      });
      //console.log( "FlexBoxPaper UpdateAngle: " + ( performance.now() - start ).toFixed(2) + 'ms');
      //$(".quick-hint").html("initial" +  Math.round(initialSum) +"  next" +  Math.round(cw) + "   Sum:" + Math.round(sum));
    };

    return FlexBoxPaper;
  })(MOCKUP.BoxPaper);
  MOCKUP.FlexBoxPaper = FlexBoxPaper;

  var Bundle = (function (_super) {
    MOCKUP.__extends(Bundle, _super);
    function Bundle(parameters, stage) {
      parameters = parameters || {};
      _super.call(this);
      this.type = "Bundle";

      if (stage != null)
        stage.add(this);
    }

    return Bundle;
  })(THREE.Group);
  MOCKUP.Bundle = Bundle;

})(MOCKUP || (MOCKUP = {}));

//Paper derivatives
(function (MOCKUP) {

  /**
   *  Ground object derived from paper.
   */
  var Ground = (function (_super) {
    MOCKUP.__extends(Ground, _super);
    function Ground(parameters, stage) {
      parameters = parameters || {};
      var _this = this;
      _this._textureRepeat = (parameters.textureRepeat == null) ? 50 : parameters.textureRepeat;
      parameters.skipMaterials = true;
      parameters.bumpScale = (parameters.bumpScale == null) ? 0.1 : parameters.bumpScale;
      parameters.shininess = (parameters.shininess == null) ? 0 : parameters.shininess;
      _super.call(this, parameters, stage);

      this.receiveShadow = true;
      this.angles = null;

      this.frontImage((parameters.frontImage == null) ? MOCKUP.defaults.groundTexture : parameters.frontImage);

      _this.type = "Ground";
    }

    /**
     * Override the frontImage function of paper Object
     */
    Ground.prototype.frontImage = function (frontImage) {
      if (frontImage == null)
        return Ground.__super.frontImage.call(this, frontImage);
      else {
        Ground.__super.frontImage.call(this, frontImage);
        /*                var texture = this.material.map;
         if (texture !== null) {
         texture.wrapS = THREE.RepeatWrapping;
         texture.wrapT = THREE.RepeatWrapping;
         texture.anisotropy = MOCKUP.defaults.anisotropy;
         this.textureRepeat(this._textureRepeat);
         }
         this.material.bumpMap = texture;
         this.bumpScale(this._bumpScale);
         this.material.needsUpdate = true;*/
      }
    };
    MOCKUP.PlanePaper.prototype.frontBump = null;

    Ground.prototype.textureRepeat = function (repeatCount) {
      if (repeatCount == null) {//get value
        return this._textureRepeat;
      }
      else {//set value
        this._textureRepeat = repeatCount;
        if (this.material.map !== null) this.material.map.repeat.set(repeatCount, repeatCount);
      }
    };

    return Ground;
  })(MOCKUP.PlanePaper);
  MOCKUP.Ground = Ground;

})(MOCKUP || (MOCKUP = {}));

MOCKUP.inchTomm = function (inch) {
  return inch * 25.4;
};
MOCKUP.mmToInch = function (mm) {
  return mm / 25.4;
};

MOCKUP.clearChild = function (child) {
  var material = child.material;
  child.parent.remove(child);
  var texture;
  if (child.dispose != null)
    child.dispose();
  if (child.geometry != null)
    child.geometry.dispose();
  if (material == null) return;

  if (material.materials == null) {
    if (material.map) {
      texture = material.map;
      material.dispose();
      texture.dispose();
    }
    if (material.bumpMap) {
      texture = material.bumpMap;
      material.dispose();
      texture.dispose();
    }

  }
  else {
    for (var matCount = 0; matCount < material.materials.length; matCount++) {
      if (material.materials[matCount]) {
        if (material.materials[matCount].map) {
          texture = material.materials[matCount].map;
          material.materials[matCount].dispose();
          texture.dispose();
        }
        if (material.materials[matCount].bumpMap) {
          texture = material.materials[matCount].bumpMap;
          material.materials[matCount].dispose();
          texture.dispose();
        }
      }

      material.materials[matCount] = null;

    }
  }
  material = null;
  texture = null;
}

/**
 * @author qiao / https://github.com/qiao
 * @author mrdoob / http://mrdoob.com
 * @author alteredq / http://alteredqualia.com/
 * @author WestLangley / http://github.com/WestLangley
 * @author erich666 / http://erichaines.com
 */
/*global THREE, console */

// This set of controls performs orbiting, dollying (zooming), and panning. It maintains
// the "up" direction as +Y, unlike the TrackballControls. Touch on tablet and phones is
// supported.
//
//    Orbit - left mouse / touch: one finger move
//    Zoom - middle mouse, or mousewheel / touch: two finger spread or squish
//    Pan - right mouse, or arrow keys / touch: three finter swipe

THREE.OrbitControls = function (object, domElement) {

  this.object = object;
  this.domElement = ( domElement != null ) ? domElement : document;

  // API

  // Set to false to disable this control
  this.enabled = true;

  // "target" sets the location of focus, where the control orbits around
  // and where it pans with respect to.
  this.target = new THREE.Vector3();

  // center is old, deprecated; use "target" instead
  this.center = this.target;

  // This option actually enables dollying in and out; left as "zoom" for
  // backwards compatibility
  this.noZoom = false;
  this.zoomSpeed = 1.0;

  // Limits to how far you can dolly in and out ( PerspectiveCamera only )
  this.minDistance = 0;
  this.maxDistance = Infinity;

  // Limits to how far you can zoom in and out ( OrthographicCamera only )
  this.minZoom = 0;
  this.maxZoom = Infinity;

  // Set to true to disable this control
  this.noRotate = false;
  this.rotateSpeed = 1.0;

  // Set to true to disable this control
  this.noPan = false;
  this.keyPanSpeed = 7.0;	// pixels moved per arrow key push

  // Set to true to automatically rotate around the target
  this.autoRotate = false;
  this.autoRotateSpeed = 2.0; // 30 seconds per round when fps is 60

  // How far you can orbit vertically, upper and lower limits.
  // Range is 0 to Math.PI radians.
  this.minPolarAngle = 0; // radians
  this.maxPolarAngle = Math.PI; // radians

  // How far you can orbit horizontally, upper and lower limits.
  // If set, must be a sub-interval of the interval [ - Math.PI, Math.PI ].
  this.minAzimuthAngle = -Infinity; // radians
  this.maxAzimuthAngle = Infinity; // radians

  // Set to true to disable use of the keys
  this.noKeys = false;

  // The four arrow keys
  this.keys = {LEFT: 37, UP: 38, RIGHT: 39, BOTTOM: 40};

  // Mouse buttons
  this.mouseButtons = {ORBIT: THREE.MOUSE.LEFT, ZOOM: THREE.MOUSE.MIDDLE, PAN: THREE.MOUSE.RIGHT};

  ////////////
  // internals

  var scope = this;

  var EPS = 0.000001;

  var rotateStart = new THREE.Vector2();
  var rotateEnd = new THREE.Vector2();
  var rotateDelta = new THREE.Vector2();

  var panStart = new THREE.Vector2();
  var panEnd = new THREE.Vector2();
  var panDelta = new THREE.Vector2();
  var panOffset = new THREE.Vector3();

  var offset = new THREE.Vector3();

  var dollyStart = new THREE.Vector2();
  var dollyEnd = new THREE.Vector2();
  var dollyDelta = new THREE.Vector2();

  var theta;
  var phi;
  var phiDelta = 0;
  var thetaDelta = 0;
  var scale = 1;
  var pan = new THREE.Vector3();

  var lastPosition = new THREE.Vector3();
  var lastQuaternion = new THREE.Quaternion();

  var STATE = {NONE: -1, ROTATE: 0, DOLLY: 1, PAN: 2, TOUCH_ROTATE: 3, TOUCH_DOLLY: 4, TOUCH_PAN: 5};

  var state = STATE.NONE;

  // for reset

  this.target0 = this.target.clone();
  this.position0 = this.object.position.clone();
  this.zoom0 = this.object.zoom;

  // so camera.up is the orbit axis

  var quat = new THREE.Quaternion().setFromUnitVectors(object.up, new THREE.Vector3(0, 1, 0));
  var quatInverse = quat.clone().inverse();

  // events

  var changeEvent = {type: 'change'};
  var startEvent = {type: 'start'};
  var endEvent = {type: 'end'};

  this.rotateLeft = function (angle) {

    if (angle == null) {

      angle = getAutoRotationAngle();

    }

    thetaDelta -= angle;

  };

  this.rotateUp = function (angle) {

    if (angle == null) {

      angle = getAutoRotationAngle();

    }

    phiDelta -= angle;

  };

  // pass in distance in world space to move left
  this.panLeft = function (distance) {

    var te = this.object.matrix.elements;

    // get X column of matrix
    panOffset.set(te[0], te[1], te[2]);
    panOffset.multiplyScalar(-distance);

    pan.add(panOffset);

  };

  // pass in distance in world space to move up
  this.panUp = function (distance) {

    var te = this.object.matrix.elements;

    // get Y column of matrix
    panOffset.set(te[4], te[5], te[6]);
    panOffset.multiplyScalar(distance);

    pan.add(panOffset);

  };

  // pass in x,y of change desired in pixel space,
  // right and down are positive
  this.pan = function (deltaX, deltaY) {

    var element = scope.domElement === document ? scope.domElement.body : scope.domElement;

    if (scope.object instanceof THREE.PerspectiveCamera) {

      // perspective
      var position = scope.object.position;
      var offset = position.clone().sub(scope.target);
      var targetDistance = offset.length();

      // half of the fov is center to top of screen
      targetDistance *= Math.tan(( scope.object.fov / 2 ) * Math.PI / 180.0);

      // we actually don't use screenWidth, since perspective camera is fixed to screen height
      scope.panLeft(2 * deltaX * targetDistance / element.clientHeight);
      scope.panUp(2 * deltaY * targetDistance / element.clientHeight);

    } else if (scope.object instanceof THREE.OrthographicCamera) {

      // orthographic
      scope.panLeft(deltaX * (scope.object.right - scope.object.left) / element.clientWidth);
      scope.panUp(deltaY * (scope.object.top - scope.object.bottom) / element.clientHeight);

    } else {

      // camera neither orthographic or perspective
      console.warn('WARNING: OrbitControls.js encountered an unknown camera type - pan disabled.');

    }

  };

  this.dollyIn = function (dollyScale) {

    if (dollyScale == null) {

      dollyScale = getZoomScale();

    }

    if (scope.object instanceof THREE.PerspectiveCamera) {

      scale /= dollyScale;

    } else if (scope.object instanceof THREE.OrthographicCamera) {

      scope.object.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, this.object.zoom * dollyScale));
      scope.object.updateProjectionMatrix();
      scope.dispatchEvent(changeEvent);

    } else {

      console.warn('WARNING: OrbitControls.js encountered an unknown camera type - dolly/zoom disabled.');

    }

  };

  this.dollyOut = function (dollyScale) {

    if (dollyScale == null) {

      dollyScale = getZoomScale();

    }

    if (scope.object instanceof THREE.PerspectiveCamera) {

      scale *= dollyScale;

    } else if (scope.object instanceof THREE.OrthographicCamera) {

      scope.object.zoom = Math.max(this.minZoom, Math.min(this.maxZoom, this.object.zoom / dollyScale));
      scope.object.updateProjectionMatrix();
      scope.dispatchEvent(changeEvent);

    } else {

      console.warn('WARNING: OrbitControls.js encountered an unknown camera type - dolly/zoom disabled.');

    }

  };

  this.update = function () {

    var position = this.object.position;

    offset.copy(position).sub(this.target);

    // rotate offset to "y-axis-is-up" space
    offset.applyQuaternion(quat);

    // angle from z-axis around y-axis

    theta = Math.atan2(offset.x, offset.z);

    // angle from y-axis

    phi = Math.atan2(Math.sqrt(offset.x * offset.x + offset.z * offset.z), offset.y);

    if (this.autoRotate && state === STATE.NONE) {

      this.rotateLeft(getAutoRotationAngle());

    }

    theta += thetaDelta;
    phi += phiDelta;

    // restrict theta to be between desired limits
    theta = Math.max(this.minAzimuthAngle, Math.min(this.maxAzimuthAngle, theta));

    // restrict phi to be between desired limits
    phi = Math.max(this.minPolarAngle, Math.min(this.maxPolarAngle, phi));

    // restrict phi to be betwee EPS and PI-EPS
    phi = Math.max(EPS, Math.min(Math.PI - EPS, phi));

    var radius = offset.length() * scale;

    // restrict radius to be between desired limits
    radius = Math.max(this.minDistance, Math.min(this.maxDistance, radius));

    // move target to panned location
    this.target.add(pan);

    offset.x = radius * Math.sin(phi) * Math.sin(theta);
    offset.y = radius * Math.cos(phi);
    offset.z = radius * Math.sin(phi) * Math.cos(theta);
    //console.log({phi:phi,theta:theta});
    // rotate offset back to "camera-up-vector-is-up" space
    offset.applyQuaternion(quatInverse);

    position.copy(this.target).add(offset);

    this.object.lookAt(this.target);

    thetaDelta = 0;
    phiDelta = 0;
    scale = 1;
    pan.set(0, 0, 0);

    // update condition is:
    // min(camera displacement, camera rotation in radians)^2 > EPS
    // using small-angle approximation cos(x/2) = 1 - x^2 / 8

    if (lastPosition.distanceToSquared(this.object.position) > EPS
      || 8 * (1 - lastQuaternion.dot(this.object.quaternion)) > EPS) {

      this.dispatchEvent(changeEvent);

      lastPosition.copy(this.object.position);
      lastQuaternion.copy(this.object.quaternion);

    }

  };


  this.reset = function () {

    state = STATE.NONE;

    this.target.copy(this.target0);
    this.object.position.copy(this.position0);
    this.object.zoom = this.zoom0;

    this.object.updateProjectionMatrix();
    this.dispatchEvent(changeEvent);

    this.update();

  };

  this.getPolarAngle = function () {

    return phi;

  };

  this.getAzimuthalAngle = function () {

    return theta

  };

  function getAutoRotationAngle() {

    return 2 * Math.PI / 60 / 60 * scope.autoRotateSpeed;

  }

  function getZoomScale() {

    return Math.pow(0.95, scope.zoomSpeed);

  }

  function onMouseDown(event) {

    if (scope.enabled === false) return;
    event.preventDefault();

    if (event.button === scope.mouseButtons.ORBIT) {
      if (scope.noRotate === true) return;

      state = STATE.ROTATE;

      rotateStart.set(event.clientX, event.clientY);

    } else if (event.button === scope.mouseButtons.ZOOM) {
      if (scope.noZoom === true) return;

      state = STATE.DOLLY;

      dollyStart.set(event.clientX, event.clientY);

    } else if (event.button === scope.mouseButtons.PAN) {
      if (scope.noPan === true) return;

      state = STATE.PAN;

      panStart.set(event.clientX, event.clientY);

    }

    if (state !== STATE.NONE) {
      document.addEventListener('mousemove', onMouseMove, false);
      document.addEventListener('mouseup', onMouseUp, false);
      document.addEventListener('mouseout', onMouseUp, false);
      scope.dispatchEvent(startEvent);
    }

  }

  function onMouseMove(event) {

    if (scope.enabled === false) return;

    event.preventDefault();

    var element = scope.domElement === document ? scope.domElement.body : scope.domElement;

    if (state === STATE.ROTATE) {

      if (scope.noRotate === true) return;

      rotateEnd.set(event.clientX, event.clientY);
      rotateDelta.subVectors(rotateEnd, rotateStart);

      // rotating across whole screen goes 360 degrees around
      scope.rotateLeft(2 * Math.PI * rotateDelta.x / element.clientWidth * scope.rotateSpeed);

      // rotating up and down along whole screen attempts to go 360, but limited to 180
      scope.rotateUp(2 * Math.PI * rotateDelta.y / element.clientHeight * scope.rotateSpeed);

      rotateStart.copy(rotateEnd);

    } else if (state === STATE.DOLLY) {

      if (scope.noZoom === true) return;

      dollyEnd.set(event.clientX, event.clientY);
      dollyDelta.subVectors(dollyEnd, dollyStart);

      if (dollyDelta.y > 0) {

        scope.dollyIn();

      } else if (dollyDelta.y < 0) {

        scope.dollyOut();

      }

      dollyStart.copy(dollyEnd);

    } else if (state === STATE.PAN) {

      if (scope.noPan === true) return;

      panEnd.set(event.clientX, event.clientY);
      panDelta.subVectors(panEnd, panStart);

      scope.pan(panDelta.x, panDelta.y);

      panStart.copy(panEnd);

    }

    if (state !== STATE.NONE) scope.update();

  }

  function onMouseUp(/* event */) {

    if (scope.enabled === false) return;

    document.removeEventListener('mousemove', onMouseMove, false);
    document.removeEventListener('mouseup', onMouseUp, false);
    document.removeEventListener('mouseout', onMouseUp, false);
    scope.dispatchEvent(endEvent);
    state = STATE.NONE;

  }

  function onMouseWheel(event) {

    if (scope.enabled === false || scope.noZoom === true || state !== STATE.NONE) return;

    event.preventDefault();
    event.stopPropagation();

    var delta = 0;

    if (event.wheelDelta != null) { // WebKit / Opera / Explorer 9

      delta = event.wheelDelta;

    } else if (event.detail != null) { // Firefox

      delta = -event.detail;

    }

    if (delta > 0) {

      scope.dollyOut();

    } else if (delta < 0) {

      scope.dollyIn();

    }

    scope.update();
    scope.dispatchEvent(startEvent);
    scope.dispatchEvent(endEvent);

    if (scope.zoomCallback) scope.zoomCallback();

  }

  function onKeyDown(event) {

    if (scope.enabled === false || scope.noKeys === true || scope.noPan === true) return;
    switch (event.keyCode) {

      case scope.keys.UP:
        scope.pan(0, scope.keyPanSpeed);
        scope.update();
        break;

      case scope.keys.BOTTOM:
        scope.pan(0, -scope.keyPanSpeed);
        scope.update();
        break;

      case scope.keys.LEFT:
        scope.pan(scope.keyPanSpeed, 0);
        scope.update();
        break;

      case scope.keys.RIGHT:
        scope.pan(-scope.keyPanSpeed, 0);
        scope.update();
        break;

    }

  }

  function touchstart(event) {

    if (scope.enabled === false) return;

    var touchCount = event.touches.length;
    if (scope.mouseButtons.PAN == THREE.MOUSE.LEFT) touchCount = 3;
    switch (touchCount) {

      case 4:	// one-fingered touch: rotate

        if (scope.noRotate === true) return;

        state = STATE.TOUCH_ROTATE;

        rotateStart.set(event.touches[0].pageX, event.touches[0].pageY);
        break;

      case 2:	// two-fingered touch: dolly

        if (scope.noZoom === true) return;

        state = STATE.TOUCH_DOLLY;

        var dx = event.touches[0].pageX - event.touches[1].pageX;
        var dy = event.touches[0].pageY - event.touches[1].pageY;
        var distance = Math.sqrt(dx * dx + dy * dy);
        dollyStart.set(0, distance);
        break;

      case 3: // three-fingered touch: pan

        if (scope.noPan === true) return;

        state = STATE.TOUCH_PAN;

        panStart.set(event.touches[0].pageX, event.touches[0].pageY);
        break;

      default:

        state = STATE.NONE;

    }

    if (state !== STATE.NONE) scope.dispatchEvent(startEvent);

  }

  function touchmove(event) {

    if (scope.enabled === false) return;

    var element = scope.domElement === document ? scope.domElement.body : scope.domElement;

    var touchCount = event.touches.length;
    // if (scope.mouseButtons.PAN == THREE.MOUSE.LEFT && touchCount == 4) touchCount = 3;
    if (scope.mouseButtons.PAN == THREE.MOUSE.LEFT) touchCount = 3;
    switch (touchCount) {

      case 4: // one-fingered touch: rotate

        if (scope.noRotate === true) return;
        if (state !== STATE.TOUCH_ROTATE) return;

        event.preventDefault();
        event.stopPropagation();

        rotateEnd.set(event.touches[0].pageX, event.touches[0].pageY);
        rotateDelta.subVectors(rotateEnd, rotateStart);

        // rotating across whole screen goes 360 degrees around
        scope.rotateLeft(2 * Math.PI * rotateDelta.x / element.clientWidth * scope.rotateSpeed);
        // rotating up and down along whole screen attempts to go 360, but limited to 180
        scope.rotateUp(2 * Math.PI * rotateDelta.y / element.clientHeight * scope.rotateSpeed);

        rotateStart.copy(rotateEnd);

        scope.update();
        break;

      case 2: // two-fingered touch: dolly

        if (scope.noZoom === true) return;
        if (state !== STATE.TOUCH_DOLLY) return;

        event.preventDefault();
        event.stopPropagation();

        var dx = event.touches[0].pageX - event.touches[1].pageX;
        var dy = event.touches[0].pageY - event.touches[1].pageY;
        var distance = Math.sqrt(dx * dx + dy * dy);

        dollyEnd.set(0, distance);
        dollyDelta.subVectors(dollyEnd, dollyStart);

        if (dollyDelta.y > 0) {

          scope.dollyOut();

        } else if (dollyDelta.y < 0) {

          scope.dollyIn();

        }

        dollyStart.copy(dollyEnd);

        scope.update();
        break;

      case 3: // three-fingered touch: pan

        if (scope.noPan === true) return;
        if (state !== STATE.TOUCH_PAN) return;

        event.preventDefault();
        event.stopPropagation();

        panEnd.set(event.touches[0].pageX, event.touches[0].pageY);
        panDelta.subVectors(panEnd, panStart);

        scope.pan(panDelta.x, panDelta.y);

        panStart.copy(panEnd);

        scope.update();
        break;

      default:

        state = STATE.NONE;

    }

  }

  function touchend(/* event */) {

    if (scope.enabled === false) return;

    scope.dispatchEvent(endEvent);
    state = STATE.NONE;

  }


  function suppressEvent(event) {
    event.preventDefault();
  }

  this.dispose = function () {
    this.domElement.removeEventListener('contextmenu', suppressEvent, false);
    this.domElement.removeEventListener('mousedown', onMouseDown, false);
    this.domElement.removeEventListener('mousewheel', onMouseWheel, false);
    this.domElement.removeEventListener('DOMMouseScroll', onMouseWheel, false); // firefox

    this.domElement.removeEventListener('touchstart', touchstart, false);
    this.domElement.removeEventListener('touchend', touchend, false);
    this.domElement.removeEventListener('touchmove', touchmove, false);

    window.removeEventListener('keydown', onKeyDown, false);
  };

  this.domElement.addEventListener('contextmenu', suppressEvent, false);
  this.domElement.addEventListener('mousedown', onMouseDown, false);
  this.domElement.addEventListener('mousewheel', onMouseWheel, false);
  this.domElement.addEventListener('DOMMouseScroll', onMouseWheel, false); // firefox

  this.domElement.addEventListener('touchstart', touchstart, false);
  this.domElement.addEventListener('touchend', touchend, false);
  this.domElement.addEventListener('touchmove', touchmove, false);

  window.addEventListener('keydown', onKeyDown, false);

  // force an update at start
  this.update();

};

THREE.OrbitControls.prototype = Object.create(THREE.EventDispatcher.prototype);
THREE.OrbitControls.prototype.constructor = THREE.OrbitControls;

THREE.CSS3DObject = function (e) {
  THREE.Object3D.call(this);
  this.element = e;
  this.element.style.position = "absolute";
  this.addEventListener("removed", function (e) {
    if (this.element.parentNode !== null) {
      this.element.parentNode.removeChild(this.element)
    }
  })
};
THREE.CSS3DObject.prototype = Object.create(THREE.Object3D.prototype);
THREE.CSS3DObject.prototype.constructor = THREE.CSS3DObject;
THREE.CSS3DSprite = function (e) {
  THREE.CSS3DObject.call(this, e)
};
THREE.CSS3DSprite.prototype = Object.create(THREE.CSS3DObject.prototype);
THREE.CSS3DSprite.prototype.constructor = THREE.CSS3DSprite;
THREE.CSS3DRenderer = function () {
  console.log("THREE.CSS3DRenderer", THREE.REVISION);
  var e, t;
  var r, i;
  var n = new THREE.Matrix4;
  var a = {camera: {fov: 0, style: ""}, objects: {}};
  var o = document.createElement("div");
  o.style.overflow = "hidden";
  o.style.WebkitTransformStyle = "preserve-3d";
  o.style.MozTransformStyle = "preserve-3d";
  o.style.oTransformStyle = "preserve-3d";
  o.style.transformStyle = "preserve-3d";
  this.domElement = o;
  var s = document.createElement("div");
  s.style.WebkitTransformStyle = "preserve-3d";
  s.style.MozTransformStyle = "preserve-3d";
  s.style.oTransformStyle = "preserve-3d";
  s.style.transformStyle = "preserve-3d";
  o.appendChild(s);
  this.setClearColor = function () {
  };
  this.getSize = function () {
    return {width: e, height: t}
  };
  this.setSize = function (n, a) {
    e = n;
    t = a;
    r = e / 2;
    i = t / 2;
    o.style.width = n + "px";
    o.style.height = a + "px";
    s.style.width = n + "px";
    s.style.height = a + "px"
  };
  var h = function (e) {
    return Math.abs(e) < Number.EPSILON ? 0 : e
  };
  var u = function (e) {
    var t = e.elements;
    return "matrix3d(" + h(t[0]) + "," + h(-t[1]) + "," + h(t[2]) + "," + h(t[3]) + "," + h(t[4]) + "," + h(-t[5]) + "," + h(t[6]) + "," + h(t[7]) + "," + h(t[8]) + "," + h(-t[9]) + "," + h(t[10]) + "," + h(t[11]) + "," + h(t[12]) + "," + h(-t[13]) + "," + h(t[14]) + "," + h(t[15]) + ")"
  };
  var c = function (e) {
    var t = e.elements;
    return "translate3d(-50%,-50%,0) matrix3d(" + h(t[0]) + "," + h(t[1]) + "," + h(t[2]) + "," + h(t[3]) + "," + h(-t[4]) + "," + h(-t[5]) + "," + h(-t[6]) + "," + h(-t[7]) + "," + h(t[8]) + "," + h(t[9]) + "," + h(t[10]) + "," + h(t[11]) + "," + h(t[12]) + "," + h(t[13]) + "," + h(t[14]) + "," + h(t[15]) + ")"
  };
  var l = function (e, t) {
    if (e instanceof THREE.CSS3DObject) {
      var r;
      if (e instanceof THREE.CSS3DSprite) {
        n.copy(t.matrixWorldInverse);
        n.transpose();
        n.copyPosition(e.matrixWorld);
        n.scale(e.scale);
        n.elements[3] = 0;
        n.elements[7] = 0;
        n.elements[11] = 0;
        n.elements[15] = 1;
        r = c(n)
      } else {
        r = c(e.matrixWorld)
      }
      var i = e.element;
      var o = a.objects[e.id];
      if (o === undefined || o !== r) {
        i.style.WebkitTransform = r;
        i.style.MozTransform = r;
        i.style.oTransform = r;
        i.style.transform = r;
        a.objects[e.id] = r
      }
      if (i.parentNode !== s) {
        s.appendChild(i)
      }
    }
    for (var h = 0, u = e.children.length; h < u; h++) {
      l(e.children[h], t)
    }
  };
  this.render = function (e, n) {
    var h = .5 / Math.tan(THREE.Math.degToRad(n.fov * .5)) * t;
    if (a.camera.fov !== h) {
      o.style.WebkitPerspective = h + "px";
      o.style.MozPerspective = h + "px";
      o.style.oPerspective = h + "px";
      o.style.perspective = h + "px";
      a.camera.fov = h
    }
    e.updateMatrixWorld();
    if (n.parent === null) n.updateMatrixWorld();
    n.matrixWorldInverse.getInverse(n.matrixWorld);
    var c = "translate3d(0,0," + h + "px)" + u(n.matrixWorldInverse) + " translate3d(" + r + "px," + i + "px, 0)";
    if (a.camera.style !== c) {
      s.style.WebkitTransform = c;
      s.style.MozTransform = c;
      s.style.oTransform = c;
      s.style.transform = c;
      a.camera.style = c
    }
    l(e, n)
  }
};