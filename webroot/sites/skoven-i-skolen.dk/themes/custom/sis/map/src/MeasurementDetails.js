import { Modal, Table, Button } from "antd";

const MeasurementDetails = ({ setModalVisible, measurements, table }) => {
  const cancel = () => {
    setModalVisible(false);
  }

  let simple = false;
  let depth = false;
  let datafiles = false;
  let title = '';

  if (table === 'depth') {
    depth = true;
    title = `Springlagsdata for ${measurements.location}`;
  } else if (table === 'simple') {
    simple = true;
    title = `Basisdata for ${measurements.location}`;
  } else {
    datafiles = true;
    title = `Komplet datasæt for ${measurements.location}`;
  }

  let simpleMeasurements = [];
  // komplet datasæt har ikke en "ui" key. Derfor opdeling i data med og uden.
  if (measurements.ui) {
    simpleMeasurements = [...Object.values(measurements.ui)];

    if ( simple === true ) {
      simpleMeasurements.sort((a, b) => a.sortorder - b.sortorder);
    }
  } else {
    simpleMeasurements = [...Object.values(measurements)];
  }

  return (
    <Modal
      visible={true}
      onCancel={() => cancel()}
      style={{ zIndex: 99999 }}
      mask={false}
      title={title}
      centered
      width={800}
      footer={[
        <Button
          key="submit"
          type="primary"
          onClick={() => cancel()}
        >
          Luk
        </Button>,
      ]}
    >

      {simple && (
        <Table
          locale={{
            filterTitle: "Filter menu",
            filterConfirm: "OK",
            filterReset: "Nulstil",
            filterEmptyText: "Ingen filtre",
            selectAll: "Vælg nuværende side",
            selectInvert: "Invertér nuværende side",
            selectionAll: "Vælg alt data",
            sortTitle: "Sortér",
            expand: "Udvid række",
            collapse: "Kollaps række",
            triggerDesc: "Sortér faldende",
            triggerAsc: "Sorter stigende",
            cancelSort: "Annullér sortering",
            emptyText: "Ingen målinger",
          }}
          size="small"
          columns={[
            {
              title: "Parameter",
              dataIndex: ["param"],
              // sorter: (a, b) => a.parameter.localeCompare(b.unit.name),
            },
            {
              title: "Enhed",
              dataIndex: ["unit"],
              // sorter: (a, b) => a.parameter.localeCompare(b.unit.name),
            },
            {
              title: "Sommer",
              dataIndex: "Sommer",
              align: 'right',
              // toFixed og toLocaleString er begge funktioner som tager et nummer og spytter en streng ud. 
              // Derfor er benyttet parseFloat for at få toFixed resultatet tilbage til et nummer, som toLocaleString så kan arbejde videre med.
              // Årsag er at vi både gerne vil have afrundet samtidig med at vi skærer ned til 1 decimal. OG vi vil have udskiftet decimaladskiller fra punktum til komma.
              // Hvis man vil skære decimaler væk i en streng bliver det svært at afrunde. 
              // Hvis man med regular expression (eller str.replace) vil skifte decimal- og tusindadskiller ud er det muligvis nemt (fra en til da : f.eks. ved at fjerne alle kommaer (tusindadskillere) og erstatte punktum(decimaladskiller) med komma )
              // Hvis man ønsker altid at vise 1 decimal, også når det er 0, kan man lade være med at bruge parseFloat og så bruge en regular expression som forklaret lige ovenfor
              // 14-10-2022 - Der er udtrykt ønske om altid at vise en decimal og derfor er parseFloat droppet
              // Har valgt at satse på at tallene ingen tusindadskiller indeholder Og at de har et punktum som decimaladskiller. Derfor kan vi blot bruge str.replace. 
              // render: (record) => record > 0 ? `${parseFloat(record.toFixed(1)).toLocaleString('da-DA')}` : '',
              render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
              // sorter: (a, b) => a.depth - b.depth,
            },
            {
              title: "Vinter",
              dataIndex: "Vinter",
              align: 'right',
              render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
              // sorter: (a, b) => a.result - b.result,
              // render: (record) => `${record.result} ${record.unit}`,
            },
          ]}
          dataSource={simpleMeasurements}
          rowKey={(e) => e.id}
          pagination={{
            hideOnSinglePage: true,
            size: "default",
            showSizeChanger: false
          }}
        />
      )}
      {depth && (
        <Table
          locale={{
            filterTitle: "Filter menu",
            filterConfirm: "OK",
            filterReset: "Nulstil",
            filterEmptyText: "Ingen filtre",
            selectAll: "Vælg nuværende side",
            selectInvert: "Invertér nuværende side",
            selectionAll: "Vælg alt data",
            sortTitle: "Sortér",
            expand: "Udvid række",
            collapse: "Kollaps række",
            triggerDesc: "Sortér faldende",
            triggerAsc: "Sorter stigende",
            cancelSort: "Annullér sortering",
            emptyText: "Ingen målinger",
          }}
          size="middle"
          columns={[
            {
              title: "Dybde (Meter)",
              dataIndex: ["depth"],
              // sorter: (a, b) => a.parameter.localeCompare(b.unit.name),
            },
            {
              title: "Temperatur (Grader C)",
              children: [
                {
                  title: 'Sommer',
                  dataIndex: 'Temperatur_sommer',
                  key: 'building',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                },
                {
                  title: 'Vinter',
                  dataIndex: 'Temperatur_vinter',
                  key: 'number',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                }
              ]
            },
            {
              title: "Salt (Promille)",
              children: [
                {
                  title: 'Sommer',
                  dataIndex: 'Salinitet_sommer',
                  key: 'building',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                },
                {
                  title: 'Vinter',
                  dataIndex: 'Salinitet_vinter',
                  key: 'number',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                }
              ]
            },
            {
              title: "Ilt (mg/l)",
              children: [
                {
                  title: 'Sommer',
                  dataIndex: 'Oxygen indhold_sommer',
                  key: 'building',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                },
                {
                  title: 'Vinter',
                  dataIndex: 'Oxygen indhold_vinter',
                  key: 'number',
                  width: 100,
                  align: 'right',
                  render: (record) => record > 0 ? `${record.toFixed(1).replace('.', ',')}` : record === 0 ? '0,0' : '',
                }
              ]
            },
          ]}
          dataSource={simpleMeasurements}
          rowKey={(e) => e.id}
          pagination={false}
          scroll={{ y: 500 }}
        // pagination={{
        //   hideOnSinglePage: true,
        //   size: "default",
        //   showSizeChanger: false
        // }}
        />
      )}

      {datafiles && (
        <Button
          href={measurements.file}
          target="_blank"
          // icon={<FilePdfOutlined />}
          size="large"
          style={{ marginRight: 10, marginBottom: 10 }}
        >
          Download datasæt
        </Button>
      )}

    </Modal>
  );
};

export default MeasurementDetails;
