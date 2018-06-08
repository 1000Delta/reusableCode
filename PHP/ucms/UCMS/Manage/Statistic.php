<?php
/**
 * 站点数据统计模块
 * 基本数据列
 * data_statistic {
 *   id INT(20) PRIMARY_KEY AUTO_INCREMENT
 *   name VARCHAR(100)
 *   count INT(20)
 * }
 */

namespace MyClass\UCMS;

use CodeLib\a\aStatistic;

class Statistic extends aStatistic {}