const mysql = require("mysql2");
const axios = require("axios");
require("dotenv").config();
const moment = require("moment");
const global = require("./global");

let checkData = async () => {
  try {
    const apiD = `${process.env.urlApi}/${process.env.numProyecto}/${process.env.clave}`;
    const response = await axios.get(apiD);
    return response.data;
  } catch (error) {
    throw new Error("Error al consultar dispositivo.", error);
  }
};

let checkParametros = async () => {
  try {
    const response = await axios.get(
      `${process.env.urlApi}/parametros/${process.env.numProyecto}/${process.env.clave}`
    );
    return response.data;
  } catch (error) {
    throw new Error("Error al consultar parámetros.", error);
  }
};

const connectionDb = mysql.createConnection(global.dataOption);
connectionDb.connect((error) => {
  if (error) {
    console.error("Error de DB");
    return;
  }
});

let sqlQuery = (query, data) => {
  return new Promise((resolve, reject) => {
    connectionDb.query(query, data, (error, result) => {
      if (error) {
        reject(201);
      } else {
        resolve(200);
      }
      connectionDb.end();
    });
  });
};

let checkDataBase = () => {
  return new Promise((resolve, reject) => {
    const checkQuery = `${process.env.dbcheckDatabaseQuery} = 'proyectoDB${process.env.numProyecto}'`;
    const createDB = `CREATE DATABASE proyectoDB${process.env.numProyecto}`;
    try {
      connectionDb.query(checkQuery, (error, result) => {
        if (error) {
          reject("Error al consultar la existencia de la BD");
        } else {
          if (result.length > 0) {
            resolve("Base de datos existente");
          } else {
            const createDbPromise = sqlQuery(createDB);
            resolve("Base de datos creada");
          }
        }
      });
    } catch (error) {
      reject("Error al verificar/crear la base de datos");
    }
  });
};

let insertTables = () => {
  return new Promise(async (resolve, reject) => {
    try {
      const tableCreationPromises = global.tables.map(async (element) => {
        try {
          await crearTablas(element);
          return "Tabla creada";
        } catch (error) {
          return "Error al crear la tabla";
        }
      });
      const results = await Promise.all(tableCreationPromises);
      resolve(results);
    } catch (error) {
      reject("Error al insertar tablas");
    }
  });
};

let crearTablas = (query) => {
  global.dataOption.database = `proyectoDB${process.env.numProyecto}`;
  return new Promise((resolve, reject) => {
    mysql.createConnection(global.dataOption).connect((error) => {
      if (error) {
        reject(404);
        return;
      } else {
        mysql
          .createConnection(global.dataOption)
          .query(query, (error, result) => {
            if (error) {
              reject(404);
            } else {
              resolve(200);
            }
            mysql.createConnection(global.dataOption).end();
          });
      }
    });
  });
};

let ingresarDispositivos = async () => {
  global.dataOption.database = `proyectoDB${process.env.numProyecto}`;

  try {
    const data = await checkData();
    const result = data.data[0];

    if (Object.keys(result).length === 0) {
      throw new Error("No se encontró un dispositivo");
    }

    if (result.vigencia === "") {
      throw new Error("No se encontró licencia");
    }

    const connection = mysql.createConnection(global.dataOption);
    connection.connect();
    const params = [
      result.id,
      result.idProyecto,
      result.clave,
      result.mqttTime,
      result.mqttHost,
      result.mqttPort,
      result.plcHost,
      result.plcPort,
      result.mysqlTime,
      result.longitud,
      result.latitud,
      moment(result.licencia).format("YYYY-MM-DD HH:mm:ss"),
    ];
    const queryS =
      "INSERT INTO dispositivos(idDB,folio, clave, mqttTime, mqttHost, mqttPort, plcHost, plcPort, mysqlTime, longitud, latitud, licencia) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    await new Promise((resolve, reject) => {
      connection.query(queryS, params, (error, result) => {
        if (error) {
          reject(new Error("Error al insertar el dispositivo"));
        } else {
          resolve(result);
        }
      });
    });
    connection.end();
    return "Dispositivo ingresado";
  } catch (error) {
    throw error;
  }
};

let insertarParametros = async () => {
  global.dataOption.database = `proyectoDB${process.env.numProyecto}`;
  const resultP = await checkParametros();
  const promises = Object.keys(resultP.data).map((key) => {
    const parametros = resultP.data[key];
    const sqlQ = `INSERT INTO parametros(idDB,dispositivo, tipo, addr, nombre, descripcion, permiso, um,clasificacion) VALUES (?,?,?,?,?,?,?,?,?)`;
    const params = [
      parametros.id,
      "1",
      parametros.tipo,
      parametros.addr,
      parametros.nombre,
      parametros.descripcion,
      parametros.permiso,
      parametros.um,
      parametros.clasificacion,
    ];
    return new Promise((resolve, reject) => {
      const connection = mysql.createConnection(global.dataOption);
      connection.connect((error) => {
        if (error) {
          reject("Error al conectar la BD");
        } else {
          connection.query(sqlQ, params, (error, result) => {
            connection.end();
            if (error) {
              reject("Error al insertar el parámetro: ");
            } else {
              resolve("Parámetro insertado");
            }
          });
        }
      });
    });
  });
  return Promise.all(promises);
};

let eliminarBaseDatos = () => {
  return new Promise((resolve, reject) => {
    const dbName = `proyectoDB${process.env.numProyecto}`;
    const dropDbQuery = `DROP DATABASE IF EXISTS ${dbName}`;

    const connection = mysql.createConnection(global.dataOption);
    connection.query(dropDbQuery, (error, result) => {
      connection.end();

      if (error) {
        console.error(
          `Error al eliminar la base de datos ${dbName}: ${error.message}`
        );
        reject(new Error(`Error al eliminar la base de datos ${dbName}`));
      } else {
        console.log(`Base de datos ${dbName} eliminada exitosamente.`);
        resolve();
      }
    });
  });
};

let main = async () => {
  try {
    // Validar si existe la base de datos
    const resultCheckResult = await checkDataBase();
    if (resultCheckResult.includes("creada")) {
      console.log("1.-", resultCheckResult);
      // insertar la tablas
      const resultInsertTables = await insertTables();
      if (resultInsertTables.length == 12) {
        console.log("2.- Tablas creadas");
        const resultDispositivo = await ingresarDispositivos();
        if (resultDispositivo.includes("ingresado")) {
          console.log("3.-", resultDispositivo);
          const resultParametros = await insertarParametros();
          if (resultParametros.length > 0) {
            console.log("4.- Instalación finalizada");
            process.exit(1);
          } else {
            console.log("Error al insertar los parametros");
            process.exit(1);
          }
        } else {
          console.log("Ocurrio un error al insertar el dispositivo");
          process.exit(1);
        }
      } else {
        console.log("Ocurrio un error al insertar todas las tablas");
        process.exit(1);
      }
    } else {
      console.log(resultCheckResult);
      process.exit(1);
    }
  } catch (error) {
    console.error("Error: ", error);
    process.exit(1);
  }
};
main();

// drop DATABASE proyectoDB41;
