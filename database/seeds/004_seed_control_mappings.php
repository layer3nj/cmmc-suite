<?php

/**
 * Seed control mappings between CMMC, NIST 800-171, and STIG
 */

return function($db) {
    $mappings = [
        // CMMC to NIST 800-171 mappings
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L1-3.1.1', 'target_framework' => 'NIST800171', 'target_code' => '3.1.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L1-3.1.2', 'target_framework' => 'NIST800171', 'target_code' => '3.1.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L1-3.1.20', 'target_framework' => 'NIST800171', 'target_code' => '3.1.20', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L1-3.1.22', 'target_framework' => 'NIST800171', 'target_code' => '3.1.22', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L2-3.1.3', 'target_framework' => 'NIST800171', 'target_code' => '3.1.3', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L2-3.1.4', 'target_framework' => 'NIST800171', 'target_code' => '3.1.4', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L2-3.1.5', 'target_framework' => 'NIST800171', 'target_code' => '3.1.5', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L2-3.1.6', 'target_framework' => 'NIST800171', 'target_code' => '3.1.6', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L3-3.1.7', 'target_framework' => 'NIST800171', 'target_code' => '3.1.7', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L3-3.1.12', 'target_framework' => 'NIST800171', 'target_code' => '3.1.12', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AC.L3-3.1.18', 'target_framework' => 'NIST800171', 'target_code' => '3.1.18', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'IA.L1-3.5.1', 'target_framework' => 'NIST800171', 'target_code' => '3.5.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'IA.L1-3.5.2', 'target_framework' => 'NIST800171', 'target_code' => '3.5.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'IA.L3-3.5.10', 'target_framework' => 'NIST800171', 'target_code' => '3.5.10', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'IA.L3-3.5.11', 'target_framework' => 'NIST800171', 'target_code' => '3.5.11', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'AT.L2-3.2.1', 'target_framework' => 'NIST800171', 'target_code' => '3.2.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AT.L2-3.2.2', 'target_framework' => 'NIST800171', 'target_code' => '3.2.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AT.L2-3.2.3', 'target_framework' => 'NIST800171', 'target_code' => '3.2.3', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'AU.L2-3.3.1', 'target_framework' => 'NIST800171', 'target_code' => '3.3.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AU.L2-3.3.2', 'target_framework' => 'NIST800171', 'target_code' => '3.3.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AU.L3-3.3.8', 'target_framework' => 'NIST800171', 'target_code' => '3.3.8', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'AU.L3-3.3.9', 'target_framework' => 'NIST800171', 'target_code' => '3.3.9', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'CM.L2-3.4.1', 'target_framework' => 'NIST800171', 'target_code' => '3.4.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'CM.L2-3.4.2', 'target_framework' => 'NIST800171', 'target_code' => '3.4.2', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'IR.L2-3.6.1', 'target_framework' => 'NIST800171', 'target_code' => '3.6.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'IR.L2-3.6.2', 'target_framework' => 'NIST800171', 'target_code' => '3.6.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'IR.L3-3.6.3', 'target_framework' => 'NIST800171', 'target_code' => '3.6.3', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'SC.L1-3.13.1', 'target_framework' => 'NIST800171', 'target_code' => '3.13.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SC.L1-3.13.5', 'target_framework' => 'NIST800171', 'target_code' => '3.13.5', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SC.L3-3.13.8', 'target_framework' => 'NIST800171', 'target_code' => '3.13.8', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SC.L3-3.13.11', 'target_framework' => 'NIST800171', 'target_code' => '3.13.11', 'relation_type' => 'implements'],

        ['source_framework' => 'CMMC', 'source_code' => 'SI.L1-3.14.1', 'target_framework' => 'NIST800171', 'target_code' => '3.14.1', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SI.L1-3.14.2', 'target_framework' => 'NIST800171', 'target_code' => '3.14.2', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SI.L1-3.14.4', 'target_framework' => 'NIST800171', 'target_code' => '3.14.4', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SI.L1-3.14.5', 'target_framework' => 'NIST800171', 'target_code' => '3.14.5', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SI.L3-3.14.6', 'target_framework' => 'NIST800171', 'target_code' => '3.14.6', 'relation_type' => 'implements'],
        ['source_framework' => 'CMMC', 'source_code' => 'SI.L3-3.14.7', 'target_framework' => 'NIST800171', 'target_code' => '3.14.7', 'relation_type' => 'implements'],

        // NIST 800-171 to STIG mappings (sample)
        ['source_framework' => 'NIST800171', 'source_code' => '3.1.1', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-000160', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.1.8', 'target_framework' => 'STIG', 'target_code' => 'WN10-AC-000010', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.3.1', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-000500', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.3.8', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-002010', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.3.8', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-002020', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.3.8', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-002030', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.5.10', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-000010', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.13.8', 'target_framework' => 'STIG', 'target_code' => 'APSC-DV-002560', 'relation_type' => 'related_to'],
        ['source_framework' => 'NIST800171', 'source_code' => '3.13.8', 'target_framework' => 'STIG', 'target_code' => 'SRG-APP-000172-WSR-000104', 'relation_type' => 'related_to'],
    ];

    foreach ($mappings as $mapping) {
        $db->insert('control_mappings', $mapping);
    }

    echo "Seeded " . count($mappings) . " control mappings\n";
};
