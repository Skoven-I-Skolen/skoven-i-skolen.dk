import React, { useState, useEffect } from "react";
import { Row, Col, Divider, Grid, Spin, Button, message } from "antd";
import 'antd/dist/antd.css';

import {
  ComposableMap,
  Geographies,
  Geography,
  ZoomableGroup,
  Marker,

} from "react-simple-maps";
import { Spring, config } from "react-spring/renderprops";
import ReactTooltip from "react-tooltip";
import MeasurementDetails from "./MeasurementDetails";
import { ExceptionMap } from "antd/lib/result";

import dataFileFlensborgFjord from "./Flensborg Fjord.xlsx";
import dataFileHorsensFjord from "./Horsens Fjord.xlsx";
import dataFileLimfjorden from "./Limfjorden.xlsx";
import dataFileMariagerFjord from "./Mariager Fjord (Dybet).xlsx";
import dataFileOdenseFjord from "./Odense Fjord.xlsx";
import dataFileOeresund from "./Øresund.xlsx";


const { useBreakpoint } = Grid;

const Map = () => {
  const [zoom, setZoom] = useState(1);
  const [center, setCenter] = useState([11.8, 56.15]);
  const [shouldAnimate, setShouldAnimate] = useState(false);

  const [markers, setMarkers] = useState([]);
  const [simpleMarkers, setSimpleMarkers] = useState([]);
  const [depthMarkers, setDepthMarkers] = useState([]);
  const [dataMarkers, setDataMarkers] = useState([]);

  const [table, setTable] = useState('simple');
  const [basicIsActive, setBasicIsActive] = useState(true);
  const [depthIsActive, setDepthIsActive] = useState(false);
  const [datafilesIsActive, setDatafilesIsActive] = useState(false);


  const [tooltip, setTooltip] = useState(null);
  const [loading, setLoading] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [activeMeasurements, setActiveMeasurements] = useState(null);
  const screens = useBreakpoint();
  const mapWidth = screens.md ? 800 : 600;
  const mapHeight = screens.md ? 600 : 800;

  /**
   * Henter data og arrangerer disse i objekter "measurements", som hver især samler målinger fra identiske geografiske positioner.
   * Hver position/measurement har følgende attributter :
   * oplysninger om positionen, type (f.eks. "sample") OG et nyt objekt som indeholder dato og resultat for seneste målinger af de forskellige stoftyper (ex: oxygen, ammounium, nitrate, phosphor)
   * Desuden er de rå data fra dataudtrækket/servicekaldet også tilføjet under key: "raw" (disse indeholder jo positionen og resultatet, så det er måske overflødigt at gentage dem som egne attributter)
   *
   * @returns array af markers - som er en array af measurements OG partners
   */
  const fetchMeasurements = async () => {
    setLoading(true);

    const response = await fetch('/sites/skoven-i-skolen.dk/themes/custom/sis/map/build/hih.txt');
    let samplesRes = await response.text();
    samplesRes = JSON.parse(samplesRes);

    // Vi har tænkt os at omorganisere de rå samples
    let measurements = {};
    let simpleMeasurements = {};
    let depthMeasurements = {};
    let completeDataSets = {};

    const manageSimpleSamples = (e, measurements) => {
      /**
       * Vi skal have en array som ser nogenlunde således ud.
       * Dette for at tilfredsstille <table> fra ant design, som
       * anvendes til visning af målingerne. Og selvfølgelig for at
       * vi kan vise data på den ønskede måde.
       * Kravet er en array bestående af objekter.
       * Eet objekt pr række i tabellen.
       * Hvert objekt indeholder key value par for hver detalje, der ønskes vist,
       *
       * Simple samples består af 2 målinger for hver stofparameter.
       * En måling for sæson sommer og en for vinter.
       * Visning i GUI skal bestå af een linje for hver stofparameter.
       * Og kolonnerne er "stofparameter", "sommer", "vinter".
       *
       * {
       *    "54.9977,10.1604" : {
       *      type: "sample",
       *      latitude: 54.9977,
       *      longitude: 10.1604,
       *      location: "Sydlige Lillebælt",
       *      ui: {
       *        "Nitrit+nitrat-N": {
       *            Sommer: 1.5,
       *            Vinter: 71,
       *            param: "Kvælstof",
       *            sortorder: 4,
       *            unit: "µg/l"
       *        },
       *         "Oxygen indhold": {
       *            Sommer: 8.729999542236328,
       *            Vinter: 3.3299999237060547,
       *            param: "Temperatur",
       *            sortorder: 1,
       *            unit: "grader C"
       *         },
       *          ......
       *      }
       *    }
       *    "54.8374,9.8245" : {
       *      .....
       *    }
       * }
       */
      // Der oprettes en key bestående af længde. og breddegrad
      // Bruges til at samle målinger fra samme geografiske position
      let key =
        e.mapLocation.latitude +
        "".substr(0, 6) +
        "," +
        e.mapLocation.longitude +
        "".substr(0, 6);

      // Hvis ny position
      if (!measurements[key]) {
        measurements[key] = {
          type: "sample",
          latitude: e.mapLocation.latitude,
          longitude: e.mapLocation.longitude,
          location: e.mapLocation.placeName,
        };
      }


      if (!measurements[key].ui) {
        measurements[key].ui = {};
      }
      if (!measurements[key].ui[e.element.name]) {
        measurements[key].ui[e.element.name] = {};
      }

      //else {
      measurements[key].ui[e.element.name][e.season] = e.result;

      switch (e.element.name) {
        case 'Salinitet':
          measurements[key].ui[e.element.name].param = 'Salt'
          measurements[key].ui[e.element.name].sortorder = 2
          break;
        case 'Temperatur':
          measurements[key].ui[e.element.name].param = 'Temperatur'
          measurements[key].ui[e.element.name].sortorder = 1
          break;
        case 'Phosphor, total-P':
          measurements[key].ui[e.element.name].param = 'Fosfor'
          measurements[key].ui[e.element.name].sortorder = 5
          break;
        case 'Oxygen indhold':
          measurements[key].ui[e.element.name].param = 'Ilt'
          measurements[key].ui[e.element.name].sortorder = 3
          break;
        case 'Nitrit+nitrat-N':
          measurements[key].ui[e.element.name].param = 'Kvælstof'
          measurements[key].ui[e.element.name].sortorder = 4
          break;
        default:
          measurements[key].ui[e.element.name].param = e.element.name
      };

      measurements[key].ui[e.element.name].unit = e.unit;

    }

    const manageDepthSamples = (e, measurements) => {

      // Der oprettes en key bestående af længde. og breddegrad
      // Bruges til at samle målinger fra samme geografiske position
      let key =
        e.mapLocation.latitude +
        "".substr(0, 6) +
        "," +
        e.mapLocation.longitude +
        "".substr(0, 6);

      // Hvis ny position
      if (!measurements[key]) {
        console.log(e)
        measurements[key] = {
          type: "sample",
          latitude: e.mapLocation.latitude,
          longitude: e.mapLocation.longitude,
          location: e.mapLocation.placeName,
        };
      }

      if (!measurements[key].ui) {
        measurements[key].ui = {};
      }

      if (Number.isInteger(e.depth)) {

        if (!measurements[key].ui[e.depth]) {
          measurements[key].ui[e.depth] = {};
        }

        measurements[key].ui[e.depth].depth = e.depth;

        if (e.season === "Sommer") {
          measurements[key].ui[e.depth][e.element.name + '_sommer'] = e.result;
        } else if (e.season === "Vinter") {
          measurements[key].ui[e.depth][e.element.name + '_vinter'] = e.result;
        }
      }

    }

    const manageCompleteDataSets = () => {
      completeDataSets = {
        1: {
          location: 'Flensborg Fjord',
          latitude: 54.837367,
          longitude: 9.8245,
          type: 'dataset',
          file: dataFileFlensborgFjord
        },
        2: {
          location: 'Limfjorden',
          latitude: 56.954,
          longitude: 9.0625,
          type: 'dataset',
          file: dataFileLimfjorden
        },
        3: {
          location: 'Odense Fjord',
          latitude: 55.479167,
          longitude: 10.519167,
          type: 'dataset',
          file: dataFileOdenseFjord
        },
        4: {
          location: 'Horsens Fjord',
          latitude: 55.844383,
          longitude: 10.0254,
          type: 'dataset',
          file: dataFileHorsensFjord
        },
        5: {
          location: 'Mariager Fjord',
          latitude: 56.662667,
          longitude: 9.973667,
          type: 'dataset',
          file: dataFileMariagerFjord
        },
        6: {
          location: 'Øresund',
          latitude: 55,
          longitude: 13.3,
          type: 'dataset',
          file: dataFileOeresund
        },
      }
    }


    if (samplesRes) {
      samplesRes.forEach((e) => {


        /**
         * Vi har brug for at filtrere i basisdata (simple) og springlagsdata (depthmeasurements) og så alle målinger, som vi jo har i forvejen.
         * Det kan ske ved at oprette en array for hvert filter.
         * Dvs vi her i starten af loopet tester om samplesRes indeholder 'x' i depthType.depthMeasurement eller depthType.simple eller begge.
         * Afhængig af depthType type pusher vi målingen i en array: measurements['all'], measurements['simple'], measurements['depth'].
         * Det er dog ikke helt så simpelt måske ? skal det være et objekt eller en array ?
         *
         * Efter tildeling i en type array, skal der egentlig fortsættes som før.
         * Derfor kan vi prøve at udskille den kode til egen funktion.
         *
         * Resultatet skal gerne blive 3 arrays eller objekter. Een for hvert filter.
         * I disse er så den opdeling i koordinater mv som hidtil er blevet anvendt.
         *
         * Lokationer kan indeholde målinger som er begge typer. Men det er ikke noget problem. Den måling kommer blot med i begge filtre.
         * I det ene filter er denne måling en simpel. I det andet er den en dybdemåling.
         *
         * Der skal tages højde for (hvis det ikke allerede sker) at en lokation kan være tom for målinger af en type, f.eks. "simple",
         * og at dette punkt så ikke skal vises på kortet, når der er klikket på "Basisdata".
         */

        if (e.depthType.simple !== "") {
          manageSimpleSamples(e, simpleMeasurements);
        }

        if (e.depthType.depthMeasurement !== "") {
          manageDepthSamples(e, depthMeasurements);
        }
      });


    }

    manageCompleteDataSets();


    // measurements indlæses i vores lokale state, så de er tilbængelige udenfor funktionen.
    // læg mærke til at measurements key (altså den der angiver geografiske position) smides væk
    // og objekt values for hver key indlæses i en array.
    // Så markers er altså en array bestående af objekter som hver især indeholder data (andre objekter) for hver geografiske position.
    // markers er den, som anvendes ved klik på en af filterknapperne. Det fremgår af handleClick nedenfor
    setMarkers([...Object.values(simpleMeasurements)]);
    setSimpleMarkers([...Object.values(simpleMeasurements)]);
    setDepthMarkers([...Object.values(depthMeasurements)]);
    setDataMarkers([...Object.values(completeDataSets)]);

       setLoading(false);
  };

  // Kører KUN een gang - ved indlæsning af siden.
  useEffect(() => {
    fetchMeasurements();
  }, []);


  const handleClick = (measurementType) => {
    if (measurementType === 'basic') {
      setMarkers(simpleMarkers);
      setTable('simple');
      setBasicIsActive(true);
      setDepthIsActive(false);
      setDatafilesIsActive(false);
    } else if (measurementType === 'depth') {
      setMarkers(depthMarkers);
      setTable('depth');
      setBasicIsActive(false);
      setDepthIsActive(true);
      setDatafilesIsActive(false);
    } else {
      setMarkers(dataMarkers);
      setTable('datafiles');
      setBasicIsActive(false);
      setDepthIsActive(false);
      setDatafilesIsActive(true);
    }

  }

  const [scaleFactor, setScaleFactor] = useState(1);

  return (
    <>
      <div className="drawer">
        <Row justify="center">
          <Col xs={24} lg={18}>

            {/* knapperne "Basisdata", "Springlagsdata" og "Komplet datasæt"  */}
            <div id="" style={{ width: screens.sm ? "160px" : "120px", height: 130, backgroundColor: '', position: 'absolute', right: 20, top: 20 }}>
              <div>
                <div>
                  <fieldset>
                    <Button
                      onClick={() => { handleClick('basic') }}
                      target="_blank"
                      size={screens.sm ? "large" : "medium"}
                      style={{ marginRight: 10, marginBottom: 5, width: screens.sm ? "160px" : "120px", textAlign: "left", backgroundColor: basicIsActive ? '#EEE' : '#EEFFFF', fontSize: screens.sm ? '' : '0.8em' }}
                    >
                      Basisdata
                    </Button>
                    <Button
                      onClick={() => { handleClick('depth') }}
                      target="_blank"
                      size={screens.sm ? "large" : "medium"}
                      style={{ marginRight: 10, marginBottom: 5, width: screens.sm ? "160px" : "120px", textAlign: "left", backgroundColor: depthIsActive ? '#EEE' : '#EEFFFF', fontSize: screens.sm ? '' : '0.8em' }}
                    >
                      Springlagsdata
                    </Button>
                    <Button
                      onClick={() => { handleClick('data') }}
                      target="_blank"
                      size= {screens.sm ? "large" : "medium"}
                      style={{ marginRight: 10, marginBottom: 10, width: screens.sm ? "160px" : "120px", textAlign: "left", backgroundColor: datafilesIsActive ? '#EEE' : '#EEFFFF', fontSize: screens.sm ? '' : '0.8em' }}
                    >
                      Komplet datasæt
                    </Button>
                  </fieldset>
                </div>
              </div>
            </div>

            <Spring
              from={{ zoom: 1, center: [11.8, 56.15] }}
              to={{ zoom: zoom, center: center }}
              config={config.slow}
            >
              {(styles) => (
                <ComposableMap
                  projection="geoAzimuthalEqualArea"
                  width={mapWidth}
                  height={mapHeight}
                  projectionConfig={{
                    rotate: [-11.8, -56.15, 0],
                    scale: screens.md ? 10500 : 8000,
                  }}
                  data-tip=""
                  style={{
                    backgroundColor: "rgb(255, 255, 255)",
                    border: "1px solid #d9d9d9",
                    borderRadius: 2,
                  }}
                >
                  <ZoomableGroup
                    // onMove og scaleFactor gør det muligt at "låse" størrelsen på punkterne på kortet når der zoomes.
                    onMove={({ k }) => setScaleFactor(k)}
                    zoom={shouldAnimate ? styles.zoom : zoom}
                    maxZoom={20}
                    center={shouldAnimate ? styles.center : center}
                    translateExtent={[
                      screens.md ? [0, 0] : [-200, -200],
                      screens.md
                        ? [mapWidth, mapHeight]
                        : [mapWidth + 200, mapHeight + 200],
                    ]}
                    onMoveEnd={(e) => {
                      setCenter(e.coordinates);
                      setZoom(e.zoom);
                    }}
                    onMoveStart={() => setShouldAnimate(false)}
                  >
                    <Geographies
                      geography={require("./denmark-municipalities.json")}
                    >
                      {({ geographies }) =>
                        geographies.map((geo) => (
                          <Geography
                            geography={geo}
                            key={geo.properties.cartodb_id}
                            strokeWidth=".5px"
                            style={{
                              default: {
                                fill: loading ? "#999" : "#333",
                                outline: "none",
                              },
                              hover: {
                                fill: "#333",
                                outline: "none",
                              },
                              pressed: {
                                fill: "#333",
                                outline: "none",
                              },
                            }}
                          />
                        ))
                      }
                    </Geographies>
                    {markers.map((e, i) =>
                      e.type === "sample" ? (
                        <Marker key={i} coordinates={[e.longitude, e.latitude]}>
                          <circle
                            // ved mobilstørrelse skærm skal punkterne skalere. division med scalefactor gør at de holder størrelsen
                            r={screens.sm ? 5 / scaleFactor : 5}
                            fill="#FFF"
                            stroke="#06F"
                            // ved mobilstørrelse skærm skal punkterne skalere.
                            strokeWidth={screens.sm ? 2.5 / scaleFactor : 2.5}
                            style={{ cursor: "pointer" }}
                            onClick={() => {
                              setActiveMeasurements(e);
                              setModalVisible(true);
                            }}
                          onMouseEnter={() => {
                            setTooltip(e);
                          }}
                          onMouseLeave={() => setTooltip(null)}
                          />
                        </Marker>
                      ) : e.latitude ? (
                        <Marker key={i} coordinates={[e.longitude, e.latitude]}>
                          <circle onClick={() => {
                            setActiveMeasurements(e);
                            setModalVisible(true);
                          }}
                            style={{ cursor: "pointer" }}
                            r={screens.sm ? 5 / scaleFactor : 5}
                            fill="#FFF"
                            stroke="#06F"
                            strokeWidth={screens.sm ? 2.5 / scaleFactor : 2.5}
                          onMouseEnter={() => {
                            setTooltip(e);
                          }}
                          onMouseLeave={() => setTooltip(null)}
                          />
                        </Marker>
                      ) : null
                    )}
                  </ZoomableGroup>
                </ComposableMap>
              )}
            </Spring>
            {loading && (
              <Row
                justify="center"
                style={{
                  position: "absolute",
                  top: mapHeight / 2,
                  width: "100%",
                }}
              >
                <Spin size="large" />
              </Row>
            )}

            <ReactTooltip
              backgroundColor="white"
            >
              {tooltip ? (
                  <>
                    <h2
                      style={{ width: "100%", textAlign: "center" }}
                    >{tooltip.location}</h2>

                  </>
                ) : null}
            </ReactTooltip>
          </Col>
        </Row>
      </div>

      {modalVisible && (
        <MeasurementDetails
          setModalVisible={setModalVisible}
          measurements={activeMeasurements}
          table={table}
        />
      )}
    </>
  );
};

export default Map;
