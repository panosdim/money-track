UPDATE
    `income` t
JOIN(
    SELECT *
    FROM
        `income`
) t2
ON
    t.id = t2.id
SET
    t.`date` = DATE_FORMAT(t.`date`, '%Y-%m-01')
